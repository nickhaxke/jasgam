<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Database;
use Core\Security\CSRF;
use PDO;
use PDOException;

class AdminProductController extends Controller
{
    public function index()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.created_at DESC
        ");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        foreach ($products as &$product) {
            if (!empty($product['image_url'])) {
                $product['image_url'] = str_replace('/public/', '/', $product['image_url']);
                if ($basePath === '' || $basePath === '/') {
                    if (strpos($product['image_url'], '/hasheem/') === 0) {
                        $product['image_url'] = substr($product['image_url'], strlen('/hasheem'));
                    }
                } elseif ($basePath !== '/hasheem' && strpos($product['image_url'], '/hasheem/') === 0) {
                    $product['image_url'] = $basePath . substr($product['image_url'], strlen('/hasheem'));
                }
            }
        }
        unset($product);
        $token = CSRF::getToken();
        return View::render('admin/products', [
            'products' => $products, 
            'csrf_token' => $token,
            'basePath' => $basePath
        ]);
    }

    public function create()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, name FROM categories ORDER BY name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        
        $token = CSRF::getToken();
        return View::render('admin/product-form', [
            'product' => null, 
            'categories' => $categories,
            'additional_images' => [],
            'csrf_token' => $token,
            'form_action' => $basePath . '/admin/products'
        ]);
    }

    public function store()
    {
        $db = Database::getInstance()->getConnection();
        
        $name = $_POST['name'] ?? '';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $description = $_POST['description'] ?? '';
        $price = (float)($_POST['price'] ?? 0);
        $category_id = (int)($_POST['category_id'] ?? 1);
        $product_type = $_POST['product_type'] ?? 'accessory';
        $game_type = ($product_type === 'game') ? ($_POST['game_type'] ?? null) : null;
        $stock = (int)($_POST['stock'] ?? 999);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $offer_percent = (int)($_POST['offer_percent'] ?? 0);
        $offer_end_date = !empty($_POST['offer_end_date']) ? $_POST['offer_end_date'] : null;
        if ($product_type === 'game') {
            $price = 0;
            $offer_percent = 0;
            $offer_end_date = null;
        }
        $image_url = null;

        // Game-specific fields
        $download_links = null;
        $download_link_labels = null;
        $tutorial_video_link = null;
        $preview_video_url = null;
        $trailer_video_url = null;
        $file_size = null;
        $is_featured = 0;
        $is_trending = 0;
        if ($product_type === 'game') {
            // Process download links
            $links = $_POST['download_links'] ?? [];
            $links = array_filter($links, function($link) { return !empty(trim($link)); });
            $download_links = !empty($links) ? json_encode(array_values($links)) : null;

            // Process download link labels
            $labels = $_POST['download_link_labels'] ?? [];
            $labels = array_filter($labels, function($label) { return !empty(trim($label)); });
            $download_link_labels = !empty($labels) ? json_encode(array_values($labels)) : null;

            // Get tutorial video link
            $tutorial_video_link = !empty($_POST['tutorial_video_link']) ? trim($_POST['tutorial_video_link']) : null;

            // New video fields
            $preview_video_url = !empty($_POST['preview_video_url']) ? trim($_POST['preview_video_url']) : null;
            $trailer_video_url = !empty($_POST['trailer_video_url']) ? trim($_POST['trailer_video_url']) : null;
            $file_size = !empty($_POST['file_size']) ? trim($_POST['file_size']) : null;
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $is_trending = isset($_POST['is_trending']) ? 1 : 0;
        }

        // Handle main image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_url = $this->uploadImage($_FILES['image']);
        }

        // Handle cover image upload
        $cover_image = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $cover_image = $this->uploadImage($_FILES['cover_image']);
        }

        // Try saving with new columns first, fallback to original columns if migration not run
        try {
            $stmt = $db->prepare("
                INSERT INTO products (name, slug, description, price, category_id, product_type, game_type, stock, is_active, image_url, cover_image, preview_video_url, trailer_video_url, file_size, is_featured, is_trending, offer_percent, offer_end_date, download_links, download_link_labels, tutorial_video_link)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $slug, $description, $price, $category_id, $product_type, $game_type, $stock, $is_active, $image_url, $cover_image, $preview_video_url, $trailer_video_url, $file_size, $is_featured, $is_trending, $offer_percent, $offer_end_date, $download_links, $download_link_labels, $tutorial_video_link]);
        } catch (\PDOException $e) {
            // Fallback: save without new columns (migration not yet run)
            $stmt = $db->prepare("
                INSERT INTO products (name, description, price, category_id, product_type, game_type, stock, is_active, image_url, offer_percent, offer_end_date, download_links, download_link_labels, tutorial_video_link)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $description, $price, $category_id, $product_type, $game_type, $stock, $is_active, $image_url, $offer_percent, $offer_end_date, $download_links, $download_link_labels, $tutorial_video_link]);
        }
        $product_id = $db->lastInsertId();
        
        // Handle additional images
        if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
            $this->uploadMultipleImages($product_id, $_FILES['images']);
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/products?success=Product created');
        exit;
    }

    public function edit($id)
    {
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            http_response_code(404);
            return View::render('errors/404');
        }

        if (!empty($product['image_url'])) {
            $product['image_url'] = str_replace('/public/', '/', $product['image_url']);
            if ($basePath === '' || $basePath === '/') {
                if (strpos($product['image_url'], '/hasheem/') === 0) {
                    $product['image_url'] = substr($product['image_url'], strlen('/hasheem'));
                }
            } elseif ($basePath !== '/hasheem' && strpos($product['image_url'], '/hasheem/') === 0) {
                $product['image_url'] = $basePath . substr($product['image_url'], strlen('/hasheem'));
            }
        }
        
        // Get additional images (table might not exist yet)
        $additional_images = [];
        try {
            $stmt = $db->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY sort_order");
            $stmt->execute([$id]);
            $additional_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($additional_images as &$img) {
                if (!empty($img['image_url'])) {
                    $img['image_url'] = str_replace('/public/', '/', $img['image_url']);
                    if ($basePath === '' || $basePath === '/') {
                        if (strpos($img['image_url'], '/hasheem/') === 0) {
                            $img['image_url'] = substr($img['image_url'], strlen('/hasheem'));
                        }
                    } elseif ($basePath !== '/hasheem' && strpos($img['image_url'], '/hasheem/') === 0) {
                        $img['image_url'] = $basePath . substr($img['image_url'], strlen('/hasheem'));
                    }
                }
            }
            unset($img);
        } catch (\PDOException $e) {
            // Table might not exist yet, that's ok
            $additional_images = [];
        }
        
        $stmt = $db->query("SELECT id, name FROM categories ORDER BY name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $token = CSRF::getToken();
        return View::render('admin/product-form', [
            'product' => $product, 
            'categories' => $categories,
            'additional_images' => $additional_images,
            'csrf_token' => $token,
            'basePath' => $basePath,
            'form_action' => $basePath . '/admin/products/update/' . (int)$id
        ]);
    }

    public function update($id)
    {
        $db = Database::getInstance()->getConnection();

        // Get current product
        $stmt = $db->prepare("SELECT image_url, cover_image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        $image_url = $current['image_url'] ?? null;
        $cover_image = $current['cover_image'] ?? null;

        $name = $_POST['name'] ?? '';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $description = $_POST['description'] ?? '';
        $price = (float)($_POST['price'] ?? 0);
        $category_id = (int)($_POST['category_id'] ?? 1);
        $product_type = $_POST['product_type'] ?? 'accessory';
        $game_type = ($product_type === 'game') ? ($_POST['game_type'] ?? null) : null;
        $stock = (int)($_POST['stock'] ?? 999);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $offer_percent = (int)($_POST['offer_percent'] ?? 0);
        $offer_end_date = !empty($_POST['offer_end_date']) ? $_POST['offer_end_date'] : null;
        if ($product_type === 'game') {
            $price = 0;
            $offer_percent = 0;
            $offer_end_date = null;
        }

        // Game-specific fields
        $download_links = null;
        $download_link_labels = null;
        $tutorial_video_link = null;
        $preview_video_url = null;
        $trailer_video_url = null;
        $file_size = null;
        $is_featured = 0;
        $is_trending = 0;
        if ($product_type === 'game') {
            // Process download links
            $links = $_POST['download_links'] ?? [];
            $links = array_filter($links, function($link) { return !empty(trim($link)); });
            $download_links = !empty($links) ? json_encode(array_values($links)) : null;

            // Process download link labels
            $labels = $_POST['download_link_labels'] ?? [];
            $labels = array_filter($labels, function($label) { return !empty(trim($label)); });
            $download_link_labels = !empty($labels) ? json_encode(array_values($labels)) : null;

            // Get tutorial video link
            $tutorial_video_link = !empty($_POST['tutorial_video_link']) ? trim($_POST['tutorial_video_link']) : null;

            // New video fields
            $preview_video_url = !empty($_POST['preview_video_url']) ? trim($_POST['preview_video_url']) : null;
            $trailer_video_url = !empty($_POST['trailer_video_url']) ? trim($_POST['trailer_video_url']) : null;
            $file_size = !empty($_POST['file_size']) ? trim($_POST['file_size']) : null;
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $is_trending = isset($_POST['is_trending']) ? 1 : 0;
        }

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_url = $this->uploadImage($_FILES['image']);
        }

        // Handle cover image upload
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $cover_image = $this->uploadImage($_FILES['cover_image']);
        }

        // Try updating with new columns first, fallback to original columns if migration not run
        try {
            $stmt = $db->prepare("
                UPDATE products
                SET name=?, slug=?, description=?, price=?, category_id=?, product_type=?, game_type=?, stock=?, is_active=?, image_url=?, cover_image=?, preview_video_url=?, trailer_video_url=?, file_size=?, is_featured=?, is_trending=?, offer_percent=?, offer_end_date=?, download_links=?, download_link_labels=?, tutorial_video_link=?, updated_at=NOW()
                WHERE id=?
            ");
            $stmt->execute([$name, $slug, $description, $price, $category_id, $product_type, $game_type, $stock, $is_active, $image_url, $cover_image, $preview_video_url, $trailer_video_url, $file_size, $is_featured, $is_trending, $offer_percent, $offer_end_date, $download_links, $download_link_labels, $tutorial_video_link, $id]);
        } catch (\PDOException $e) {
            // Fallback: update without new columns (migration not yet run)
            $stmt = $db->prepare("
                UPDATE products
                SET name=?, description=?, price=?, category_id=?, product_type=?, game_type=?, stock=?, is_active=?, image_url=?, offer_percent=?, offer_end_date=?, download_links=?, download_link_labels=?, tutorial_video_link=?, updated_at=NOW()
                WHERE id=?
            ");
            $stmt->execute([$name, $description, $price, $category_id, $product_type, $game_type, $stock, $is_active, $image_url, $offer_percent, $offer_end_date, $download_links, $download_link_labels, $tutorial_video_link, $id]);
        }
        
        // Handle additional images
        if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
            $this->uploadMultipleImages($id, $_FILES['images']);
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/products?success=Product updated');
        exit;
    }

    public function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/products?success=Product deleted');
        exit;
    }

    public function activate($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE products SET is_active = 1, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/products');
        exit;
    }


    private function uploadImage($file)
    {
        // Validate file
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            return null;
        }
        
        if ($file['size'] > 2 * 1024 * 1024) { // 2MB max
            return null;
        }
        
        // Create uploads directory if it doesn't exist
        $uploadDir = __DIR__ . '/../../uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            return $basePath . '/uploads/products/' . $filename;
        }
        
        return null;
    }

    private function uploadMultipleImages($product_id, $files)
    {
        $db = Database::getInstance()->getConnection();
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $uploadDir = __DIR__ . '/../../uploads/products/';
        
        // Limit to 6 images
        $count = min(6, count($files['name']));
        $sort_order = 1;
        
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            
            // Validate file
            if (!in_array($files['type'][$i], $allowed)) {
                continue;
            }
            
            if ($files['size'][$i] > 2 * 1024 * 1024) {
                continue;
            }
            
            // Generate unique filename
            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
            $filepath = $uploadDir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                $config = require __DIR__ . '/../../config/app.php';
                $basePath = rtrim($config['base_path'] ?? '', '/');
                $image_url = $basePath . '/uploads/products/' . $filename;
                
                // Insert into product_images table
                $stmt = $db->prepare("
                    INSERT INTO product_images (product_id, image_url, sort_order)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$product_id, $image_url, $sort_order]);
                $sort_order++;
            }
        }
    }

    public function deactivate($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE products SET is_active = 0, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/products');
        exit;
    }
}
