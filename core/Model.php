<?php
namespace Core;

use PDO;

abstract class Model
{
    protected static PDO $db;

    public function __construct()
    {
        if (!isset(self::$db)) {
            $config = require __DIR__ . '/../config/config.php';
            self::$db = new PDO(
                $config['db']['dsn'],
                $config['db']['user'],
                $config['db']['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
    }

    protected function db(): PDO
    {
        return self::$db;
    }
}