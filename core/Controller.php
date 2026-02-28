<?php
namespace Core;

use Core\View;

class Controller
{
    protected function view(string $view, array $data = [])
    {
        View::render($view, $data);
    }

    protected function redirect(string $url)
    {
        header('Location: ' . $url);
        exit;
    }

    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}