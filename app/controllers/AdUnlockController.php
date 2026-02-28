<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;

class AdUnlockController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->ensureAdUnlockTable();
    }

    private function ensureAdUnlockTable(): void
    {
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS ad_unlocks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                token VARCHAR(64) NOT NULL UNIQUE,
                download_link TEXT NOT NULL,
                ip_address VARCHAR(64) NULL,
                user_agent VARCHAR(255) NULL,
                created_at DATETIME NOT NULL,
                expires_at DATETIME NOT NULL,
                used_at DATETIME NULL,
                INDEX idx_product_ip (product_id, ip_address),
                INDEX idx_token (token),
                INDEX idx_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (\Throwable $e) {
            // Continue with session-only unlock if DB create fails.
        }
    }

    public function unlock()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        if ($productId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Product ID required']);
            exit;
        }

        $stmt = $this->db->prepare("SELECT id, name, product_type, download_links, download_link_labels FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$product || ($product['product_type'] ?? '') !== 'game') {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Game not found']);
            exit;
        }

        $links = !empty($product['download_links'])
            ? (json_decode($product['download_links'], true) ?? [])
            : [];
        $labels = !empty($product['download_link_labels'])
            ? (json_decode($product['download_link_labels'], true) ?? [])
            : [];
        $downloadLink = $links[0] ?? null;

        if (empty($downloadLink)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Download link not available']);
            exit;
        }

        if (!isset($_SESSION['ad_game_access']) || !is_array($_SESSION['ad_game_access'])) {
            $_SESSION['ad_game_access'] = [];
        }
        $_SESSION['ad_game_access'][$productId] = time() + 600;

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $token = null;

        try {
            $reuseStmt = $this->db->prepare("
                SELECT token
                FROM ad_unlocks
                WHERE product_id = ?
                  AND ip_address = ?
                  AND expires_at > NOW()
                ORDER BY created_at DESC
                LIMIT 1
            ");
            $reuseStmt->execute([$productId, $ip]);
            $existing = $reuseStmt->fetch(\PDO::FETCH_ASSOC);

            if ($existing) {
                $token = $existing['token'];
            } else {
                $token = bin2hex(random_bytes(24));

                $insert = $this->db->prepare("
                    INSERT INTO ad_unlocks (product_id, token, download_link, ip_address, user_agent, created_at, expires_at)
                    VALUES (?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 10 MINUTE))
                ");
                $insert->execute([$productId, $token, $downloadLink, $ip, $userAgent]);

                // Increment download count (first-time unlock only)
                try {
                    $this->db->prepare("UPDATE products SET download_count = download_count + 1 WHERE id = ?")
                        ->execute([$productId]);
                } catch (\Throwable $e) { /* column may not exist yet */ }
            }
        } catch (\Throwable $e) {
            $token = null;
        }

        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        $downloadUrl = $token ? ($basePath . '/download/' . $token) : $downloadLink;

        echo json_encode([
            'success' => true,
            'download_url' => $downloadUrl,
            'links' => array_values($links),
            'labels' => array_values($labels),
            'expires_in' => 600
        ]);
        exit;
    }
}
