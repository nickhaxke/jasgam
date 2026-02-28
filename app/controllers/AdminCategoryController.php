<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Database;
use Core\Security\CSRF;
use PDO;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        
        $token = CSRF::getToken();
        return View::render('admin/categories', [
            'categories' => $categories,
            'csrf_token' => $token,
            'basePath' => $basePath
        ]);
    }

    public function create()
    {
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        
        $token = CSRF::getToken();
        return View::render('admin/categories-form', [
            'category' => null,
            'csrf_token' => $token,
            'basePath' => $basePath,
            'isEdit' => false
        ]);
    }

    public function store()
    {
        $name = trim($_POST['name'] ?? '');
        $slug = $this->generateSlug($name);

        if (empty($name)) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header("Location: {$basePath}/admin/categories/create?error=" . urlencode('Category name is required'));
            exit;
        }

        $db = Database::getInstance()->getConnection();
        
        // Check if slug already exists
        $stmt = $db->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug = $slug . '-' . time();
        }

        $stmt = $db->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);

        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header("Location: {$basePath}/admin/categories?success=" . urlencode('Category created successfully'));
        exit;
    }

    public function edit($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header("Location: {$basePath}/admin/categories?error=" . urlencode('Category not found'));
            exit;
        }

        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        
        $token = CSRF::getToken();
        return View::render('admin/categories-form', [
            'category' => $category,
            'csrf_token' => $token,
            'basePath' => $basePath,
            'isEdit' => true
        ]);
    }

    public function update($id)
    {
        $name = trim($_POST['name'] ?? '');
        
        if (empty($name)) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header("Location: {$basePath}/admin/categories/edit/{$id}?error=" . urlencode('Category name is required'));
            exit;
        }

        $db = Database::getInstance()->getConnection();
        
        // Get current category
        $stmt = $db->prepare("SELECT slug FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header("Location: {$basePath}/admin/categories?error=" . urlencode('Category not found'));
            exit;
        }

        // Generate new slug
        $slug = $this->generateSlug($name);
        
        // Check if slug exists (but not for current category)
        $stmt = $db->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $id]);
        if ($stmt->fetch()) {
            $slug = $slug . '-' . time();
        }

        $stmt = $db->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $id]);

        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header("Location: {$basePath}/admin/categories?success=" . urlencode('Category updated successfully'));
        exit;
    }

    public function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        
        // Check if category has products
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        
        if ($result['count'] > 0) {
            header("Location: {$basePath}/admin/categories?error=" . urlencode('Cannot delete category with products. Remove products first.'));
            exit;
        }

        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: {$basePath}/admin/categories?success=" . urlencode('Category deleted successfully'));
        exit;
    }

    private function generateSlug($name)
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}
