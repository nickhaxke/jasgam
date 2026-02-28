<?php

namespace Core;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $config = require __DIR__ . '/../config/app.php';
        $db = $config['db'];
        try {
            $this->connection = new \PDO(
                "mysql:host={$db['host']};dbname={$db['name']};charset={$db['charset']}",
                $db['user'],
                $db['pass'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            die('Database Connection Error: ' . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (\PDOException $e) {
            die('Query Error: ' . $e->getMessage());
        }
    }

    public static function initDB()
    {
        $db = self::getInstance()->getConnection();
        
        // Products table
        $db->exec("
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                category TEXT NOT NULL,
                price REAL NOT NULL,
                stock INTEGER NOT NULL DEFAULT 0,
                description TEXT,
                active BOOLEAN DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Users table
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT,
                role TEXT DEFAULT 'user',
                blocked BOOLEAN DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Orders table
        $db->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                user_name TEXT NOT NULL,
                item_count INTEGER NOT NULL,
                total REAL NOT NULL,
                status TEXT DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(user_id) REFERENCES users(id)
            )
        ");

        // Check if products table is empty and seed it
        $stmt = $db->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            self::seedDatabase();
        }
    }

    private static function seedDatabase()
    {
        $db = self::getInstance()->getConnection();
        
        $products = [
            ['Cyber Legends', 'Action', 59.99, 15, 'Immerse yourself in the neon-soaked streets of Neo-Tokyo with stunning graphics and intense action gameplay.', 1],
            ['Neon Drift', 'Racing', 49.99, 8, 'Experience high-speed racing through cyberpunk cities with advanced physics and stunning visuals.', 1],
            ['Quantum Zero', 'Puzzle', 59.99, 0, 'Mind-bending quantum puzzles that will challenge your brain and perception of reality.', 0],
            ['Shadow Protocol', 'Action', 54.99, 12, 'Stealth and espionage in a dystopian future. Outsmart AI and complete covert missions.', 1],
            ['Galactic Arena', 'Shooter', 39.99, 25, 'Battle across cosmic arenas with futuristic weapons and epic multiplayer experiences.', 1],
            ['Eclipse Force', 'RPG', 44.99, 18, 'Epic RPG adventure in a world of eternal darkness broken only by mysterious celestial events.', 1],
            ['Void Echo', 'Adventure', 49.99, 12, 'Explore the vastness of space and uncover ancient mysteries in this stunning adventure game.', 1],
            ['Nexus Wars', 'Strategy', 54.99, 20, 'Strategic warfare in a futuristic setting. Build alliances, manage resources, and conquer worlds.', 1],
            ['Plasma Rush', 'Action', 39.99, 30, 'Fast-paced action where time moves differently. Manipulate plasma and defy physics.', 1],
            ['Obsidian Dreams', 'Puzzle', 34.99, 15, 'Beautiful puzzle game with surreal visuals and mind-bending challenges.', 1],
        ];

        $stmt = $db->prepare("
            INSERT INTO products (name, category, price, stock, description, active)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($products as $product) {
            $stmt->execute($product);
        }

        // Seed users
        $users = [
            ['Admin User', 'admin@hasheem.com', password_hash('admin123', PASSWORD_BCRYPT), 'admin', 0],
            ['John Doe', 'john@example.com', password_hash('password123', PASSWORD_BCRYPT), 'user', 0],
            ['Jane Smith', 'jane@example.com', password_hash('password123', PASSWORD_BCRYPT), 'user', 0],
        ];

        $userStmt = $db->prepare("
            INSERT INTO users (name, email, password, role, blocked)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($users as $user) {
            $userStmt->execute($user);
        }

        // Seed orders
        $orders = [
            [1, 'John Doe', 2, 109.98, 'completed'],
            [3, 'Jane Smith', 1, 59.99, 'pending'],
            [1, 'John Doe', 3, 154.97, 'shipped'],
        ];

        $orderStmt = $db->prepare("
            INSERT INTO orders (user_id, user_name, item_count, total, status)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($orders as $order) {
            $orderStmt->execute($order);
        }
    }
}
