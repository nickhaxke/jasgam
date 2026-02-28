<?php
namespace Core;

class Env
{
    public static function load($file)
    {
        if (!file_exists($file)) return;
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = array_map('trim', explode('=', $line, 2));
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
                putenv("$name=$value");
            }
        }
    }

    public static function get($key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
