<?php
namespace Core\Security;

use Core\Session;

class CSRF
{
    private const TOKEN_KEY = '_csrf_token';
    private const TOKEN_TIMESTAMP_KEY = '_csrf_token_time';
    private const TOKEN_LIFETIME = 28800; // 8 hours (instead of 1 hour)

    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set(self::TOKEN_KEY, $token);
        Session::set(self::TOKEN_TIMESTAMP_KEY, time());
        return $token;
    }

    public static function getToken(): string
    {
        $token = Session::get(self::TOKEN_KEY);
        $timestamp = Session::get(self::TOKEN_TIMESTAMP_KEY);
        
        // If token doesn't exist or has expired, generate new one
        if (!$token || !$timestamp || (time() - $timestamp > self::TOKEN_LIFETIME)) {
            return self::generateToken();
        }
        
        return $token;
    }

    public static function validateToken($token): bool
    {
        $sessionToken = Session::get(self::TOKEN_KEY);
        $timestamp = Session::get(self::TOKEN_TIMESTAMP_KEY);
        
        // Token must exist, be non-empty, and not be expired
        if (!$token || !$sessionToken || !$timestamp) {
            return false;
        }
        
        // Check if token is within lifetime
        if (time() - $timestamp > self::TOKEN_LIFETIME) {
            return false;
        }
        
        // Validate using hash_equals for timing attack protection
        return hash_equals($sessionToken, $token);
    }

    public static function refreshToken(): void
    {
        self::generateToken();
    }
}
