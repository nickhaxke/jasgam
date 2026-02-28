<?php
namespace Core;

class Auth
{
    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function login($user)
    {
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
    }

    public static function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
    }
}