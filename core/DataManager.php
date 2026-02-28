<?php

namespace Core;

use PDO;

class DataManager
{
    private static $db = null;

    private static function getDB()
    {
        if (self::$db === null) {
            self::$db = Database::getInstance()->getConnection();
        }
        return self::$db;
    }

    // PRODUCTS
    public static function getData($type)
    {
        $db = self::getDB();
        
        if ($type === 'products') {
            $stmt = $db->query("SELECT * FROM products ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($type === 'users') {
            $stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($type === 'orders') {
            $stmt = $db->query("SELECT * FROM orders ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return [];
    }

    public static function addProduct($product)
    {
        $db = self::getDB();
        
        $stmt = $db->prepare("
            INSERT INTO products (name, category, price, stock, description, active)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $product['name'],
            $product['category'],
            $product['price'],
            $product['stock'],
            $product['description'] ?? '',
            $product['active'] ? 1 : 0
        ]);
    }

    public static function updateProduct($id, $product)
    {
        $db = self::getDB();
        
        $fields = [];
        $values = [];
        
        foreach ($product as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = ?";
                if ($key === 'active') {
                    $values[] = $value ? 1 : 0;
                } else {
                    $values[] = $value;
                }
            }
        }
        
        $values[] = $id;
        
        $sql = "UPDATE products SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        return $stmt->execute($values);
    }

    public static function deleteProduct($id)
    {
        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // USERS
    public static function updateUser($id, $user)
    {
        $db = self::getDB();
        
        $fields = [];
        $values = [];
        
        foreach ($user as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = ?";
                if ($key === 'blocked') {
                    $values[] = $value ? 1 : 0;
                } else {
                    $values[] = $value;
                }
            }
        }
        
        $values[] = $id;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        return $stmt->execute($values);
    }

    public static function deleteUser($id)
    {
        $db = self::getDB();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ORDERS
    public static function updateOrder($id, $order)
    {
        $db = self::getDB();
        
        $fields = [];
        $values = [];
        
        foreach ($order as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $id;
        
        $sql = "UPDATE orders SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        return $stmt->execute($values);
    }
}
