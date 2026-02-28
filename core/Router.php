<?php
namespace Core;

use Core\Session;

class Router
{
    protected array $routes = [];
    private $notFoundHandler;
    protected string $basePath = '';
    protected array $legacyBasePaths = [];
    protected array $paramRoutes = [];

    public function __construct()
    {
        $config = require __DIR__ . '/../config/app.php';
        $this->basePath = rtrim($config['base_path'] ?? '', '/');
        $this->legacyBasePaths = $config['legacy_base_paths'] ?? [];
    }

    public function get(string $uri, $handler, array $middleware = [])
    {
        $this->addRoute('GET', $uri, $handler, $middleware);
    }

    public function post(string $uri, $handler, array $middleware = [])
    {
        $this->addRoute('POST', $uri, $handler, $middleware);
    }

    public function addRoute(string $method, string $uri, $handler, array $middleware = [])
    {
        if (strpos($uri, '{') !== false) {
            $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $uri);
            $pattern = '#^' . rtrim($pattern, '/') . '/?$#';
            $this->paramRoutes[$method][] = [
                'pattern' => $pattern,
                'handler' => $handler,
                'middleware' => $middleware
            ];
        } else {
            $this->routes[$method][$this->normalizeUri($uri)] = [
                'handler' => $handler,
                'middleware' => $middleware
            ];
        }
    }

    /**
     * @param callable|string $handler
     */
    public function setNotFound($handler)
    {
        if (!is_callable($handler) && !is_string($handler)) {
            throw new \InvalidArgumentException('Not found handler must be a callable or controller@method string');
        }
        $this->notFoundHandler = $handler;
    }

    public function dispatch(string $method, string $uri)
    {
        try {
            // Online cleanup: redirect legacy prefixed GET URLs (e.g. /hasheem/cart -> /cart)
            if ($method === 'GET' && $this->basePath === '') {
                $path = parse_url($uri, PHP_URL_PATH) ?? '/';
                $query = parse_url($uri, PHP_URL_QUERY);

                foreach ($this->legacyBasePaths as $legacyPath) {
                    $legacyPath = rtrim((string)$legacyPath, '/');
                    if ($legacyPath === '') {
                        continue;
                    }

                    if ($path === $legacyPath || strpos($path, $legacyPath . '/') === 0) {
                        $cleanPath = substr($path, strlen($legacyPath));
                        $cleanPath = '/' . ltrim($cleanPath, '/');
                        $target = rtrim($cleanPath, '/') ?: '/';
                        if (!empty($query)) {
                            $target .= '?' . $query;
                        }

                        header('Location: ' . $target, true, 302);
                        exit;
                    }
                }
            }

            $uri = $this->normalizeUri($uri);
            $route = $this->routes[$method][$uri] ?? null;
            $params = [];
            $middleware = [];

            // Dynamic param routes
            if (!$route && !empty($this->paramRoutes[$method])) {
                foreach ($this->paramRoutes[$method] as $paramRoute) {
                    if (preg_match($paramRoute['pattern'], $uri, $matches)) {
                        $route = $paramRoute;
                        foreach ($matches as $k => $v) {
                            if (!is_int($k)) $params[$k] = $v;
                        }
                        break;
                    }
                }
            }

            if (!$route) {
                if (isset($this->notFoundHandler)) {
                    return $this->invoke($this->notFoundHandler);
                }
                http_response_code(404);
                require __DIR__ . '/../views/errors/404.php';
                exit;
            }

            $middleware = $route['middleware'] ?? [];
            foreach ($middleware as $mw) {
                // Handle role:admin syntax
                if (strpos($mw, ':') !== false) {
                    [$type, $value] = explode(':', $mw);
                    if ($type === 'role') {
                        $user = \Core\Session::get('user');
                        if (!$user || ($user['role'] ?? null) !== $value) {
                            http_response_code(403);
                            require __DIR__ . '/../views/errors/404.php';
                            exit;
                        }
                    }
                } else {
                    // Handle regular middleware classes
                    $mwClass = '\Core\Middleware\\' . ucfirst($mw) . 'Middleware';
                    if (class_exists($mwClass)) {
                        $mwClass::handle();
                    }
                }
            }

            // CSRF check for POST
            if ($method === 'POST') {
                $token = $_POST['_token'] ?? $_POST['csrf_token'] ?? '';
                
                // Debug logging
                error_log("CSRF Check - Method: POST");
                error_log("CSRF Check - Token from POST: " . substr($token, 0, 20) . "...");
                error_log("CSRF Check - Session Token: " . substr(Session::get('_csrf_token') ?? 'none', 0, 20) . "...");
                
                if (!\Core\Security\CSRF::validateToken($token)) {
                    error_log("CSRF Token validation FAILED");
                    
                    // Return JSON error for AJAX requests
                    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
                    $expectsJson = !empty($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

                    if ($isAjax || $expectsJson) {
                        http_response_code(419);
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Security token expired. Please refresh the page.']);
                        exit;
                    }
                    
                    http_response_code(419);
                    $error = 'Security token expired. Please refresh and try again.';
                    require __DIR__ . '/../views/errors/419.php';
                    exit;
                }
                
                error_log("CSRF Token validation PASSED");
            }

            $handler = $route['handler'];
            return $this->invoke($handler, $params);
        } catch (\Throwable $e) {
            $this->logError($e);
            http_response_code(500);
            require __DIR__ . '/../views/errors/500.php';
            exit;
        }
    }

    protected function invoke($handler, $params = [])
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        if (is_string($handler)) {
            [$controller, $method] = explode('@', $handler);
            $controller = '\App\Controllers\\' . $controller;
            if (class_exists($controller)) {
                $instance = new $controller;
                if (method_exists($instance, $method)) {
                    return call_user_func_array([$instance, $method], $params);
                }
            }
        }
        http_response_code(500);
        require __DIR__ . '/../views/errors/500.php';
        exit;
    }

    protected function normalizeUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        // Primary base-path stripping
        if ($this->basePath && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }

        // Compatibility: allow legacy hardcoded prefixes (e.g. /hasheem/*) to work
        foreach ($this->legacyBasePaths as $legacyPath) {
            $legacyPath = rtrim((string)$legacyPath, '/');
            if ($legacyPath === '' || $legacyPath === $this->basePath) {
                continue;
            }

            if ($uri === $legacyPath) {
                $uri = '/';
                break;
            }

            if (strpos($uri, $legacyPath . '/') === 0) {
                $uri = substr($uri, strlen($legacyPath));
                break;
            }
        }

        $uri = '/' . ltrim($uri, '/');
        return rtrim($uri, '/') ?: '/';
    }

    protected function logError($e)
    {
        $log = '[' . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
        $logFile = __DIR__ . '/../storage/logs/app.log';
        file_put_contents($logFile, $log, FILE_APPEND);
    }
}
