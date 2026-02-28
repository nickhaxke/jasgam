<?php
// Manual class autoloader
spl_autoload_register(function ($class) {
    $prefixes = [
        'Core\\'        => __DIR__ . '/core/',
        'App\\Controllers\\' => __DIR__ . '/app/controllers/',
        'App\\Models\\'      => __DIR__ . '/app/models/',
    ];
    foreach ($prefixes as $prefix => $baseDir) {
        if (strpos($class, $prefix) === 0) {
            $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
            $file = $baseDir . $relative . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// Load helper functions
require_once __DIR__ . '/core/Helpers.php';

use Core\Router;
use Core\Session;
use Core\ErrorHandler;
use Core\Env;
use Core\Database;


// Load environment variables
Env::load(__DIR__ . '/.env');

// Error display based on environment
$appEnv = Env::get('APP_ENV', 'production');
if ($appEnv === 'local' || $appEnv === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

ErrorHandler::register();

// Configure session settings BEFORE starting session
if (php_sapi_name() !== 'cli') {
    ini_set('session.gc_maxlifetime', 3600); // 1 hour
    ini_set('session.cookie_lifetime', 0); // Expires when browser closes
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');
}

Session::start();

try {
    $router = new Router();
    // Database tables already created via setup-mysql.php (using MySQL schema.sql)
    // Database::initDB();
    require __DIR__ . '/routes/web.php';

    $router->setNotFound(function() {
        http_response_code(404);
        require __DIR__ . '/views/errors/404.php';
        exit;
    });

    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    http_response_code(500);
    echo "<pre>";
    echo "FATAL ERROR CAUGHT IN INDEX.PHP\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nTrace:\n";
    echo $e->getTraceAsString();
    echo "</pre>";
    exit;
}
