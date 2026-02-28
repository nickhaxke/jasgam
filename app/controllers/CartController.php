<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Cart;
use Core\Security\CSRF;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::items();
        $token = CSRF::getToken();
        
        // Enrich cart items with latest product info from DB
        foreach ($cartItems as $id => $item) {
            $product = Product::find($id);
            if ($product) {
                $cartItems[$id]['price'] = $product['price'];
                $cartItems[$id]['stock'] = $product['stock'];
                $cartItems[$id]['name'] = $product['name'];
            }
        }
        
        return View::render('cart/index', [
            'items' => $cartItems,
            'summary' => Cart::summary(),
            'csrf_token' => $token
        ]);
    }
    
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/products?error=Invalid request');
            exit;
        }
        
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        
        if (!$productId) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/products?error=Invalid product');
            exit;
        }
        
        $product = Product::find($productId);
        if (!$product) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/products?error=Product not found');
            exit;
        }
        
        // Check stock
        if ($product['stock'] < $quantity) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/product/' . $productId . '?error=Not enough stock available');
            exit;
        }
        
        Cart::add($productId, $quantity, $product);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/cart?success=Product added');
        exit;
    }
    
    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/cart?error=Invalid request');
            exit;
        }
        
        $productId = (int)($_POST['product_id'] ?? 0);
        Cart::remove($productId);
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/cart?success=Item removed');
        exit;
    }
    
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/cart?error=Invalid request');
            exit;
        }
        
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(0, (int)($_POST['quantity'] ?? 0));
        
        if ($quantity <= 0) {
            Cart::remove($productId);
        } else {
            Cart::update($productId, $quantity);
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/cart?success=Cart updated');
        exit;
    }
    
    public function clear()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/cart?error=Invalid request');
            exit;
        }
        
        Cart::clear();
        
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/cart?success=Cart cleared');
        exit;
    }
}
