<?php

namespace App\Models;

use Core\Database;
use PDO;

class Product
{
    /**
     * Get latest products from database
     */
    public static function latest(int $limit = 8)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get all products
     */
    public static function all()
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get product by ID
     */
    public static function find(int $id)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get products by category
     */
    public static function byCategory(string $category, int $limit = null)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT * FROM products WHERE category = ? AND is_active = 1 ORDER BY id DESC";
            if ($limit) {
                $sql .= " LIMIT " . intval($limit);
            }
            $stmt = $db->prepare($sql);
            $stmt->execute([$category]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Search products
     */
    public static function search(string $query)
    {
        try {
            $db = Database::getInstance()->getConnection();
            $searchTerm = "%" . $query . "%";
            $stmt = $db->prepare("SELECT * FROM products WHERE is_active = 1 AND (name LIKE ? OR description LIKE ?) ORDER BY id DESC");
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }
}

