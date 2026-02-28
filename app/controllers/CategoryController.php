<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class CategoryController extends Controller
{
    public function index($slug = null)
    {
        $categories = [];
        return View::render('category/index', ['categories' => $categories]);
    }
}
