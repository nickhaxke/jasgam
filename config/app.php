<?php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = str_replace('\\', '/', dirname($scriptName));
$scriptDir = trim($scriptDir, '/');

// Auto-detect base path from the running front-controller path:
// localhost example: /hasheem/index.php => /hasheem
// online root example: /index.php => ''
$detectedBasePath = ($scriptDir === '' || $scriptDir === '.') ? '' : '/' . $scriptDir;

return [
    'base_path' => $detectedBasePath,
    // Compatibility prefix(es) accepted by router (legacy hardcoded links)
    'legacy_base_paths' => ['/hasheem'],
    'env' => 'local',
    'db' => [
        'host' => 'localhost',
        'name' => 'hasheem',
        'user' => 'root',
        'pass' => '/OEGwfI6]WxXSSSt',
        'charset' => 'utf8mb4',
    ],
    'csrf_token_key' => '_csrf',
];
