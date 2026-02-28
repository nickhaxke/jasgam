<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Database;
use PDO;

class ProductController extends Controller
{
    public function index()
    {
        // /products redirects to games list
        $config   = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/games');
        exit;
    }

    public function games()
    {
        $db = Database::getInstance()->getConnection();

        $platform = trim($_GET['platform'] ?? '');
        $search   = trim($_GET['q'] ?? '');
        $category = (int)($_GET['category'] ?? 0);

        // Build query
        $where  = ["p.is_active = 1", "p.product_type = 'game'"];
        $params = [];

        if ($platform !== '') {
            $where[]  = "p.game_type = ?";
            $params[] = $platform;
        }
        if ($search !== '') {
            $where[]  = "(p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($category > 0) {
            $where[]  = "p.category_id = ?";
            $params[] = $category;
        }

        $whereSQL = implode(' AND ', $where);
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE $whereSQL
            ORDER BY p.game_type ASC, p.name ASC
        ");
        $stmt->execute($params);
        $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($games as &$g) {
            $g['image_url'] = $this->normalizeImageUrl($g['image_url'] ?? null);
        }
        unset($g);

        // Platforms for filter tabs
        $pStmt = $db->query("
            SELECT DISTINCT game_type
            FROM products
            WHERE is_active = 1 AND product_type = 'game' AND game_type IS NOT NULL
            ORDER BY game_type ASC
        ");
        $platforms = array_column($pStmt->fetchAll(PDO::FETCH_ASSOC), 'game_type');

        // Categories for sidebar
        $cStmt = $db->query("SELECT * FROM categories ORDER BY name ASC");
        $categories = $cStmt->fetchAll(PDO::FETCH_ASSOC);

        return View::render('product/games', [
            'title'            => 'Games' . ($platform ? " – $platform" : '') . ' | JUSGAM',
            'games'            => $games,
            'platforms'        => $platforms,
            'categories'       => $categories,
            'active_platform'  => $platform,
            'active_category'  => $category,
            'search_query'     => $search,
        ]);
    }

    public function show($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ? AND p.is_active = 1
        ");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404);
            return View::render('errors/404');
        }

        $product['image_url'] = $this->normalizeImageUrl($product['image_url'] ?? null);
        $product['cover_image'] = $this->normalizeImageUrl($product['cover_image'] ?? null);

        // Parse download links
        $downloadLinks  = !empty($product['download_links'])
            ? (json_decode($product['download_links'], true) ?? [])
            : [];
        $downloadLabels = !empty($product['download_link_labels'])
            ? (json_decode($product['download_link_labels'], true) ?? [])
            : [];

        // Check if this visitor already unlocked via ad (session or DB)
        $isUnlocked  = false;
        $unlockedExp = 0;

        if (!empty($_SESSION['ad_game_access'][$id]) && (int)$_SESSION['ad_game_access'][$id] > time()) {
            $isUnlocked  = true;
            $unlockedExp = (int)$_SESSION['ad_game_access'][$id];
        }

        if (!$isUnlocked) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            if ($ip) {
                try {
                    $uStmt = $db->prepare("
                        SELECT UNIX_TIMESTAMP(expires_at) as exp
                        FROM ad_unlocks
                        WHERE product_id = ? AND ip_address = ? AND expires_at > NOW()
                        ORDER BY created_at DESC LIMIT 1
                    ");
                    $uStmt->execute([$id, $ip]);
                    $row = $uStmt->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        $isUnlocked  = true;
                        $unlockedExp = (int)$row['exp'];
                    }
                } catch (\PDOException $e) {
                    // ad_unlocks table not yet created – that's fine
                }
            }
        }

        // Related games (same platform)
        $relStmt = $db->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.product_type = 'game'
              AND p.game_type = ? AND p.id != ?
            ORDER BY RAND() LIMIT 6
        ");
        $relStmt->execute([$product['game_type'] ?? '', $id]);
        $related = $relStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($related as &$r) {
            $r['image_url'] = $this->normalizeImageUrl($r['image_url'] ?? null);
        }
        unset($r);

        // Screenshots from product_images table
        $screenshots = [];
        try {
            $ssStmt = $db->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
            $ssStmt->execute([$id]);
            $screenshots = $ssStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($screenshots as &$ss) {
                $ss['image_url'] = $this->normalizeImageUrl($ss['image_url'] ?? null);
            }
            unset($ss);
        } catch (\PDOException $e) { /* table may not exist yet */ }

        // Most downloaded games
        $mostDownloaded = [];
        try {
            $mdStmt = $db->prepare("
                SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND p.product_type = 'game'
                  AND p.download_count > 0 AND p.id != ?
                ORDER BY p.download_count DESC
                LIMIT 6
            ");
            $mdStmt->execute([$id]);
            $mostDownloaded = $mdStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($mostDownloaded as &$md) {
                $md['image_url'] = $this->normalizeImageUrl($md['image_url'] ?? null);
            }
            unset($md);
        } catch (\PDOException $e) { /* column may not exist yet */ }

        // Recently added games
        $recentlyAdded = [];
        $raStmt = $db->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.product_type = 'game' AND p.id != ?
            ORDER BY p.created_at DESC
            LIMIT 6
        ");
        $raStmt->execute([$id]);
        $recentlyAdded = $raStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($recentlyAdded as &$ra) {
            $ra['image_url'] = $this->normalizeImageUrl($ra['image_url'] ?? null);
        }
        unset($ra);

        $config   = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');

        return View::render('product/show', [
            'title'           => htmlspecialchars($product['name']) . ' | JUSGAM',
            'product'         => $product,
            'download_links'  => $downloadLinks,
            'download_labels' => $downloadLabels,
            'is_unlocked'     => $isUnlocked,
            'unlocked_exp'    => $unlockedExp,
            'related'         => $related,
            'screenshots'     => $screenshots,
            'most_downloaded' => $mostDownloaded,
            'recently_added'  => $recentlyAdded,
        ]);
    }

    /**
     * AJAX live search endpoint: GET /api/search?q=...
     */
    public function apiSearch()
    {
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            return $this->json(['results' => []]);
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT p.id, p.name, p.game_type, p.image_url, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.product_type = 'game'
              AND (p.name LIKE ? OR p.description LIKE ?)
            ORDER BY p.name ASC
            LIMIT 8
        ");
        $search = "%{$q}%";
        $stmt->execute([$search, $search]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $config   = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');

        foreach ($results as &$r) {
            $r['image_url'] = $this->normalizeImageUrl($r['image_url'] ?? null);
            $r['url'] = $basePath . '/product/' . $r['id'];
        }
        unset($r);

        return $this->json(['results' => $results]);
    }

    private function normalizeImageUrl(?string $url): ?string
    {
        if (empty($url)) return $url;
        $config   = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
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
