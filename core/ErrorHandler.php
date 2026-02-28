<?php
namespace Core;

class ErrorHandler
{
    public static function register()
    {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        $msg = "Error [$errno]: $errstr in $errfile on line $errline";
        self::logError($msg);
        http_response_code(500);
        if (self::isDev()) {
            echo "<pre style='color:red'>" . htmlspecialchars($msg) . "\n" . htmlspecialchars(print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true)) . "</pre>";
        } else {
            include __DIR__ . '/../views/errors/500.php';
        }
        return true;
    }

    public static function handleException($exception)
    {
        $msg = 'Uncaught Exception: ' . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
        $trace = $exception->getTraceAsString();
        self::logError($msg . "\n" . $trace);
        http_response_code(500);
        if (self::isDev()) {
            echo "<pre style='color:red'>" . htmlspecialchars($msg) . "\n" . htmlspecialchars($trace) . "</pre>";
        } else {
            include __DIR__ . '/../views/errors/500.php';
        }
    }

    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $msg = 'Fatal Error: ' . $error['message'] . " in " . $error['file'] . " on line " . $error['line'];
            self::logError($msg);
            http_response_code(500);
            if (self::isDev()) {
                echo "<pre style='color:red'>" . htmlspecialchars($msg) . "</pre>";
            } else {
                include __DIR__ . '/../views/errors/500.php';
            }
        }
    }

    protected static function logError($message)
    {
        $logFile = __DIR__ . '/../storage/logs/error.log';
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
        error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, $logFile);
    }

    protected static function isDev()
    {
        return ($_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'production') !== 'production';
    }
}
