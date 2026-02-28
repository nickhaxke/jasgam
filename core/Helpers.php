<?php

/**
 * URL Helper - Generates URLs with proper base path for localhost and online deployment
 * 
 * Usage:
 *   url('login')        → /hasheem/login (localhost) or /login (online)
 *   url('/products')    → /hasheem/products or /products
 *   url('cart')         → /hasheem/cart or /cart
 */

function url($path = '') {
    // Load config once and cache it
    static $basePath = null;
    
    if ($basePath === null) {
        $config = require __DIR__ . '/../config/app.php';
        $basePath = $config['base_path'] ?? '';
    }
    
    // Remove leading slashes
    $path = ltrim($path, '/');
    
    // Online deployment (empty base_path)
    if (empty($basePath)) {
        return '/' . $path;
    }
    
    // Localhost deployment (has base_path)
    return rtrim($basePath, '/') . '/' . $path;
}

/**
 * Asset Helper - Generates asset URLs with proper base path
 * 
 * Usage:
 *   asset('css/home.css')    → /hasheem/assets/css/home.css or /assets/css/home.css
 *   asset('js/script.js')    → /hasheem/assets/js/script.js or /assets/js/script.js
 */

function asset($path = '') {
    return url('assets/' . ltrim($path, '/'));
}

?>
