<?php

/**
 * Lightweight Zero-Dependency .env Loader for Komune Platform
 */

if (!function_exists('load_env_file')) {
    function load_env_file($filePath)
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            // Ignore comments and empty lines
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Handle single/double quoted values
                $len = strlen($value);
                if ($len >= 2) {
                    if (($value[0] === '"' && $value[$len - 1] === '"') || 
                        ($value[0] === "'" && $value[$len - 1] === "'")) {
                        $value = substr($value, 1, -1);
                    }
                }

                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv("$name=$value");
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
        return true;
    }
}

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $val = getenv($key);
        if ($val === false) {
            $val = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }

        if (is_string($val)) {
            $lower = strtolower($val);
            if ($lower === 'true' || $lower === '(true)') return true;
            if ($lower === 'false' || $lower === '(false)') return false;
            if ($lower === 'null' || $lower === '(null)') return null;
            if ($lower === 'empty' || $lower === '(empty)') return '';
        }

        return $val ?? $default;
    }
}

// Automatically load root .env
$rootEnv = dirname(dirname(__DIR__)) . '/.env';
load_env_file($rootEnv);
