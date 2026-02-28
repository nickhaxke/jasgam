<?php
namespace Core\Middleware;

use Core\Session;

class AuthMiddleware
{
    public static function handle()
    {
        if (!Session::get('user')) {
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . '/login');
            exit;
        }
    }
}
