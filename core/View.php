<?php
namespace Core;

class View
{
    public static function render(string $view, array $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . str_replace('.', '/', $view) . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($view);
            exit;
        }
    }
}