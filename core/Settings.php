<?php

namespace Core;

class Settings
{
    private static bool $loaded = false;
    private static array $cache = [];

    private static function ensureTable(): void
    {
        $db = Database::getInstance()->getConnection();
        $db->exec("
            CREATE TABLE IF NOT EXISTS settings (
                `key` VARCHAR(100) PRIMARY KEY,
                `value` TEXT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    }

    private static function load(): void
    {
        if (self::$loaded) {
            return;
        }
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT `key`, `value` FROM settings");
        self::$cache = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            self::$cache[$row['key']] = $row['value'];
        }
        self::$loaded = true;
    }

    public static function get(string $key, $default = null)
    {
        self::load();
        return self::$cache[$key] ?? $default;
    }

    public static function all(): array
    {
        self::load();
        return self::$cache;
    }

    public static function setMany(array $pairs): void
    {
        self::ensureTable();
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("REPLACE INTO settings (`key`, `value`) VALUES (?, ?)");
        foreach ($pairs as $key => $value) {
            $stmt->execute([$key, $value]);
        }
        self::$loaded = false;
        self::load();
    }
}
