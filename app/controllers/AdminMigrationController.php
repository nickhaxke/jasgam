<?php

namespace App\Controllers;

use Core\Auth;
use Core\Controller;
use Core\Database;

class AdminMigrationController extends Controller
{
    protected $db;
    protected $migrationsPath;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->migrationsPath = __DIR__ . '/../../storage/migrations';

        // Check if user is authenticated and is admin
        if (!Auth::user() || Auth::user()['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Display all available migrations
     */
    public function index()
    {
        try {
            $migrations = [];
            $executedMigrations = $this->getExecutedMigrations();

            // Get all SQL migration files
            $files = glob($this->migrationsPath . '/*.sql');
            
            foreach ($files as $file) {
                $filename = basename($file);
                $isExecuted = in_array($filename, $executedMigrations);
                
                $migrations[] = [
                    'filename' => $filename,
                    'executed' => $isExecuted,
                    'file' => $file,
                    'size' => filesize($file)
                ];
            }

            return $this->view('admin/migrations', [
                'migrations' => $migrations,
                'executedMigrations' => $executedMigrations
            ]);
        } catch (\Exception $e) {
            error_log("[MIGRATION ERROR] " . $e->getMessage());
            return $this->view('errors/500', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Run a specific migration
     */
    public function run($migration)
    {
        header('Content-Type: application/json');
        
        try {
            // Validate migration name to prevent directory traversal
            if (strpos($migration, '..') !== false || strpos($migration, '/') !== false) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid migration name']);
                exit;
            }

            $migrationFile = $this->migrationsPath . '/' . $migration;

            // Check if file exists
            if (!file_exists($migrationFile)) {
                http_response_code(404);
                echo json_encode(['error' => 'Migration file not found']);
                exit;
            }

            // Read and execute migration
            $sql = file_get_contents($migrationFile);
            
            if (empty($sql)) {
                http_response_code(400);
                echo json_encode(['error' => 'Migration file is empty']);
                exit;
            }

            // Remove comments and split by semicolon
            $sql = $this->removeComments($sql);
            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                function ($s) {
                    return !empty($s) && strlen($s) > 2;
                }
            );

            $executed = 0;
            $errors = [];

            foreach ($statements as $statement) {
                try {
                    $this->db->exec($statement);
                    $executed++;
                } catch (\PDOException $e) {
                    // Log but continue with other statements
                    $errors[] = $e->getMessage();
                    error_log("[MIGRATION STATEMENT ERROR] " . $e->getMessage());
                }
            }

            // Log the migration as executed
            $this->logMigration($migration);

            error_log("[MIGRATION EXECUTED] $migration - $executed statements by admin " . Auth::user()['id']);

            echo json_encode([
                'success' => true,
                'message' => "✅ Migration '$migration' executed! ($executed statements)",
                'errors' => !empty($errors) ? $errors : null
            ]);
            exit;
            
        } catch (\Exception $e) {
            http_response_code(500);
            error_log("[MIGRATION ERROR] " . $e->getMessage() . "\n" . $e->getTraceAsString());
            echo json_encode([
                'error' => 'Migration failed: ' . $e->getMessage(),
                'type' => basename(str_replace('\\', '/', get_class($e)))
            ]);
            exit;
        }
    }

    /**
     * Remove SQL comments
     */
    private function removeComments($sql)
    {
        // Remove single-line comments (-- comment)
        $sql = preg_replace('/^\s*--.*$/m', '', $sql);
        // Remove multi-line comments (/* comment */)
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        return $sql;
    }

    /**
     * Get list of executed migrations
     */
    private function getExecutedMigrations()
    {
        try {
            // Try to read from migrations log file
            $logFile = __DIR__ . '/../../storage/migrations_executed.log';
            
            if (!file_exists($logFile)) {
                return [];
            }

            $content = file_get_contents($logFile);
            return array_filter(
                array_map('trim', explode("\n", $content)),
                function ($line) {
                    return !empty($line) && strpos($line, '#') !== 0;
                }
            );
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Log executed migration
     */
    private function logMigration($migration)
    {
        try {
            $logFile = __DIR__ . '/../../storage/migrations_executed.log';
            $logDir = dirname($logFile);
            
            // Create directory if it doesn't exist
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }

            // Check if already logged
            $executed = $this->getExecutedMigrations();
            if (!in_array($migration, $executed)) {
                file_put_contents($logFile, $migration . "\n", FILE_APPEND);
            }
        } catch (\Exception $e) {
            error_log("[MIGRATION LOG ERROR] " . $e->getMessage());
        }
    }
}
