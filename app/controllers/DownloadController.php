<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;
use Core\View;

class DownloadController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function download($token)
    {
        $stmt = $this->db->prepare("SELECT * FROM ad_unlocks WHERE token = ? LIMIT 1");
        $stmt->execute([$token]);
        $unlock = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$unlock) {
            http_response_code(404);
            return View::render('errors/404');
        }

        if (!empty($unlock['used_at'])) {
            http_response_code(403);
            return View::render('errors/404');
        }

        if (!empty($unlock['expires_at']) && strtotime($unlock['expires_at']) < time()) {
            http_response_code(403);
            return View::render('errors/404');
        }

        $update = $this->db->prepare("UPDATE ad_unlocks SET used_at = NOW() WHERE id = ?");
        $update->execute([$unlock['id']]);

        header('Location: ' . $unlock['download_link'], true, 302);
        exit;
    }

    /**
     * Show ad interstitial page before download
     */
    public function interstitial()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Get product ID and link index from query params
        $productId = (int)($_GET['product'] ?? 0);
        $linkIndex = (int)($_GET['link'] ?? 0);
        
        if ($productId <= 0) {
            http_response_code(400);
            return View::render('errors/404');
        }
        
        // Check if user has access to this product
        $hasAccess = false;
        
        // Check session access
        if (isset($_SESSION['ad_game_access']) && is_array($_SESSION['ad_game_access'])) {
            $sessionExpiry = (int)($_SESSION['ad_game_access'][$productId] ?? 0);
            $hasAccess = $sessionExpiry > time();
        }
        
        // Check database access
        if (!$hasAccess) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            if (!empty($ip)) {
                $stmt = $this->db->prepare("
                    SELECT id 
                    FROM ad_unlocks 
                    WHERE product_id = ? 
                      AND ip_address = ? 
                      AND expires_at > NOW() 
                    LIMIT 1
                ");
                $stmt->execute([$productId, $ip]);
                $hasAccess = (bool)$stmt->fetch(\PDO::FETCH_ASSOC);
            }
        }
        
        if (!$hasAccess) {
            http_response_code(403);
            return View::render('errors/404');
        }
        
        // Get product and download links
        $stmt = $this->db->prepare("
            SELECT download_links, download_link_labels 
            FROM products 
            WHERE id = ? AND is_active = 1
        ");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$product) {
            http_response_code(404);
            return View::render('errors/404');
        }
        
        $links = json_decode($product['download_links'] ?? '[]', true) ?? [];
        $labels = json_decode($product['download_link_labels'] ?? '[]', true) ?? [];
        
        if (!isset($links[$linkIndex])) {
            http_response_code(404);
            return View::render('errors/404');
        }
        
        $downloadLink = $links[$linkIndex];
        $downloadLabel = $labels[$linkIndex] ?? 'Download Link';
        
        return View::render('download/interstitial', [
            'downloadLink' => $downloadLink,
            'downloadLabel' => $downloadLabel,
        ]);
    }
}
