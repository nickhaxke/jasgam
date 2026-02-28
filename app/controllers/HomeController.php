<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Database;
use PDO;

class HomeController extends Controller
{
    public function index()
    {
        $db = Database::getInstance()->getConnection();

        // Latest 12 games across all platforms
        $stmt = $db->query("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.product_type = 'game'
            ORDER BY p.created_at DESC
            LIMIT 12
        ");
        $latestGames = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Featured games for hero slider
        $featuredGames = [];
        try {
            $stmt = $db->query("
                SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND p.product_type = 'game' AND p.is_featured = 1
                ORDER BY p.created_at DESC
                LIMIT 6
            ");
            $featuredGames = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) { /* column may not exist yet */ }

        // Trending games
        $trendingGames = [];
        try {
            $stmt = $db->query("
                SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND p.product_type = 'game' AND p.is_trending = 1
                ORDER BY p.created_at DESC
                LIMIT 10
            ");
            $trendingGames = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) { /* column may not exist yet */ }

        // Most downloaded games
        $mostDownloaded = [];
        try {
            $stmt = $db->query("
                SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND p.product_type = 'game' AND p.download_count > 0
                ORDER BY p.download_count DESC
                LIMIT 10
            ");
            $mostDownloaded = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) { /* column may not exist yet */ }

        // All games grouped by platform (game_type)
        $stmt = $db->query("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.product_type = 'game'
            ORDER BY p.game_type ASC, p.name ASC
        ");
        $allGames = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fix image URLs
        $config   = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');

        foreach ($latestGames as &$g) {
            $g['image_url'] = $this->normalizeUrl($g['image_url'] ?? null, $basePath);
            $g['cover_image'] = $this->normalizeUrl($g['cover_image'] ?? null, $basePath);
        }
        unset($g);
        foreach ($featuredGames as &$g) {
            $g['image_url'] = $this->normalizeUrl($g['image_url'] ?? null, $basePath);
            $g['cover_image'] = $this->normalizeUrl($g['cover_image'] ?? null, $basePath);
        }
        unset($g);
        foreach ($trendingGames as &$g) {
            $g['image_url'] = $this->normalizeUrl($g['image_url'] ?? null, $basePath);
            $g['cover_image'] = $this->normalizeUrl($g['cover_image'] ?? null, $basePath);
        }
        unset($g);
        foreach ($mostDownloaded as &$g) {
            $g['image_url'] = $this->normalizeUrl($g['image_url'] ?? null, $basePath);
            $g['cover_image'] = $this->normalizeUrl($g['cover_image'] ?? null, $basePath);
        }
        unset($g);
        foreach ($allGames as &$g) {
            $g['image_url'] = $this->normalizeUrl($g['image_url'] ?? null, $basePath);
        }
        unset($g);

        // Group by platform
        $byPlatform = [];
        foreach ($allGames as $game) {
            $type = strtoupper(trim($game['game_type'] ?? 'OTHER'));
            $byPlatform[$type][] = $game;
        }

        // Categories
        $stmt = $db->query("SELECT * FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Stats
        $totalGames = count($allGames);
        $stmt = $db->query("SELECT COUNT(DISTINCT game_type) as cnt FROM products WHERE is_active=1 AND product_type='game'");
        $platformCount = (int)($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);

        return View::render('home/index', [
            'title'           => 'JUSGAM – Free Games Portal',
            'featured_games'  => $featuredGames,
            'trending_games'  => $trendingGames,
            'most_downloaded' => $mostDownloaded,
            'latest_games'    => $latestGames,
            'by_platform'     => $byPlatform,
            'categories'      => $categories,
            'total_games'     => $totalGames,
            'platform_count'  => $platformCount,
        ]);
    }

    private function normalizeUrl(?string $url, string $basePath): ?string
    {
        if (empty($url)) return $url;
        $url = str_replace('/public/', '/', $url);
        if (($basePath === '' || $basePath === '/') && strpos($url, '/hasheem/') === 0) {
            return substr($url, strlen('/hasheem'));
        }
        if ($basePath !== '/hasheem' && strpos($url, '/hasheem/') === 0) {
            return $basePath . substr($url, strlen('/hasheem'));
        }
        return $url;
    }
}
