<?php

namespace Core;

class Cart
{
    private const SESSION_KEY = 'cart_items';

    /**
     * Initialize session
     */
    private static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    /**
     * Add item to cart
     */
    public static function add(int $productId, int $quantity = 1, array $product = [])
    {
        self::init();

        if (isset($_SESSION[self::SESSION_KEY][$productId])) {
            $_SESSION[self::SESSION_KEY][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION[self::SESSION_KEY][$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'name' => $product['name'] ?? 'Product',
                'price' => $product['price'] ?? 0,
                'category' => $product['category'] ?? '',
                'added_at' => date('Y-m-d H:i:s')
            ];
        }

        return true;
    }

    /**
     * Remove item from cart
     */
    public static function remove(int $productId)
    {
        self::init();
        unset($_SESSION[self::SESSION_KEY][$productId]);
        return true;
    }

    /**
     * Update quantity
     */
    public static function update(int $productId, int $quantity)
    {
        self::init();
        if (isset($_SESSION[self::SESSION_KEY][$productId])) {
            if ($quantity <= 0) {
                self::remove($productId);
            } else {
                $_SESSION[self::SESSION_KEY][$productId]['quantity'] = $quantity;
            }
            return true;
        }
        return false;
    }

    /**
     * Get all items
     */
    public static function items()
    {
        self::init();
        return $_SESSION[self::SESSION_KEY] ?? [];
    }

    /**
     * Get specific item
     */
    public static function get(int $productId)
    {
        self::init();
        return $_SESSION[self::SESSION_KEY][$productId] ?? null;
    }

    /**
     * Get item count
     */
    public static function count()
    {
        self::init();
        return count($_SESSION[self::SESSION_KEY] ?? []);
    }

    /**
     * Get total items quantity
     */
    public static function totalQuantity()
    {
        self::init();
        $total = 0;
        foreach ($_SESSION[self::SESSION_KEY] ?? [] as $item) {
            $total += $item['quantity'];
        }
        return $total;
    }

    /**
     * Get cart total
     */
    public static function total()
    {
        self::init();
        $total = 0;
        foreach ($_SESSION[self::SESSION_KEY] ?? [] as $item) {
            $total += ($item['price'] * $item['quantity']);
        }
        return round($total, 2);
    }

    /**
     * Clear cart
     */
    public static function clear()
    {
        self::init();
        $_SESSION[self::SESSION_KEY] = [];
        return true;
    }

    /**
     * Check if empty
     */
    public static function isEmpty()
    {
        self::init();
        return empty($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Get cart summary for display
     */
    public static function summary()
    {
        self::init();
        return [
            'items' => self::items(),
            'count' => self::count(),
            'total_quantity' => self::totalQuantity(),
            'total' => self::total(),
            'is_empty' => self::isEmpty()
        ];
    }
}
