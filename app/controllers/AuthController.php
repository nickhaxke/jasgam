<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Session;
use Core\Database;
use Core\Security\CSRF;
use PDO;

class AuthController extends Controller
{
    public function showLogin()
    {
        $token = \Core\Security\CSRF::getToken();
        return View::render('auth/login', ['csrf_token' => $token]);
    }
    
    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['blocked']) {
                $token = CSRF::getToken();
                return View::render('auth/login', ['error' => 'Your account has been blocked', 'csrf_token' => $token]);
            }
            
            // Regenerate session ID to prevent fixation attacks
            session_regenerate_id(true);
            
            Session::set('user', [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]);
            
            // Regenerate CSRF token after login
            CSRF::generateToken();
            
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . ($user['role'] === 'admin' ? '/admin' : '/'));
            exit;
        } else {
            $token = CSRF::getToken();
            return View::render('auth/login', ['error' => 'Invalid email or password', 'csrf_token' => $token]);
        }
    }
    
    public function showRegister()
    {
        $token = \Core\Security\CSRF::getToken();
        return View::render('auth/register', ['csrf_token' => $token]);
    }
    
    public function register()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $role = 'user';
        
        if (empty($name) || empty($email) || empty($password)) {
            $token = \Core\Security\CSRF::getToken();
            return View::render('auth/register', ['error' => 'All fields are required', 'csrf_token' => $token]);
        }
        
        if ($password !== $passwordConfirm) {
            $token = \Core\Security\CSRF::getToken();
            return View::render('auth/register', ['error' => 'Passwords do not match', 'csrf_token' => $token]);
        }
        
        // Check if email already exists
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $token = \Core\Security\CSRF::getToken();
            return View::render('auth/register', ['error' => 'Email already registered', 'csrf_token' => $token]);
        }
        
        // Insert new user into database
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $insertStmt = $db->prepare("
            INSERT INTO users (name, email, password_hash, role, blocked)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        if ($insertStmt->execute([$name, $email, $hashedPassword, $role, 0])) {
            $userId = $db->lastInsertId();
            
            // Regenerate session ID
            session_regenerate_id(true);
            
            Session::set('user', [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'role' => $role
            ]);
            
            // Regenerate CSRF token after registration
            CSRF::generateToken();
            
            $config = require __DIR__ . '/../../config/app.php';
            $basePath = rtrim($config['base_path'] ?? '', '/');
            header('Location: ' . $basePath . ($role === 'admin' ? '/admin' : '/'));
            exit;
        } else {
            $token = \Core\Security\CSRF::getToken();
            return View::render('auth/register', ['error' => 'Registration failed. Please try again.', 'csrf_token' => $token]);
        }
    }
    
    public function logout()
    {
        Session::destroy();
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/');
    }
    
    public function showForgot()
    {
        $token = \Core\Security\CSRF::getToken();
        return View::render('auth/forgot', ['csrf_token' => $token]);
    }
    
    public function forgot()
    {
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/login');
    }
    
    public function showReset($token)
    {
        $csrfToken = \Core\Security\CSRF::getToken();
        return View::render('auth/reset', ['csrf_token' => $csrfToken]);
    }
    
    public function reset($token)
    {
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/');
    }
    
    public function verify($token)
    {
        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/login');
    }
}