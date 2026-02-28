<?php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = str_replace('\\', '/', dirname($scriptName));
$scriptDir = trim($scriptDir, '/');

// Auto-detect base path from the running front-controller path:
// localhost example: /hasheem/index.php => /hasheem
// online root example: /index.php => ''
$detectedBasePath = ($scriptDir === '' || $scriptDir === '.') ? '' : '/' . $scriptDir;

// Helper to read env with fallback
$env = function($key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
};

return [
    'base_path' => $detectedBasePath,
    // Compatibility prefix(es) accepted by router (legacy hardcoded links)
    'legacy_base_paths' => ['/hasheem'],
    'env' => $env('APP_ENV', 'local'),
    'db' => [
        'host'    => $env('DB_HOST', 'localhost'),
        'name'    => $env('DB_NAME', 'hasheem'),
        'user'    => $env('DB_USER', 'root'),
        'pass'    => $env('DB_PASS', ''),
        'charset' => $env('DB_CHARSET', 'utf8mb4'),
    ],
    'csrf_token_key' => '_csrf',
];
