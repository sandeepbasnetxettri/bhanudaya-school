<?php
/**
 * Environment Variables Loader
 * 
 * This file loads environment variables from the .env file
 */

function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos($line, '#') === 0) {
            continue;
        }

        // Parse key-value pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes from value if present
            if ((startsWith($value, '"') && endsWith($value, '"')) ||
                (startsWith($value, "'") && endsWith($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            // Set the environment variable
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function endsWith($haystack, $needle) {
    return substr($haystack, -strlen($needle)) === $needle;
}

// Load the environment variables
loadEnv(__DIR__ . '/../.env');

// Set default values if environment variables are not set
if (!getenv('DB_HOST')) {
    putenv('DB_HOST=localhost');
    $_ENV['DB_HOST'] = 'localhost';
}

if (!getenv('DB_NAME')) {
    putenv('DB_NAME=school_management');
    $_ENV['DB_NAME'] = 'school_management';
}

if (!getenv('DB_USER')) {
    putenv('DB_USER=root');
    $_ENV['DB_USER'] = 'root';
}

if (!getenv('DB_PASS')) {
    putenv('DB_PASS=');
    $_ENV['DB_PASS'] = '';
}

if (!getenv('APP_URL')) {
    putenv('APP_URL=http://localhost/Bhanudayamodelschool');
    $_ENV['APP_URL'] = 'http://localhost/Bhanudayamodelschool';
}