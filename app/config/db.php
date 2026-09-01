<?php

require_once __DIR__ . '/env.php';

// DATABASE CONFIGURATION (SISTEM KOMUNE)
$user = env("DB_USER", "root");
$password = env("DB_PASS", "");
$database = env("DB_NAME", "komune");
$database2 = env("DB_CORE_NAME", "billing_core");
$hostname = env("DB_HOST", "127.0.0.1");

// 1. Primary Database Connection: komune (Operational & Financial DB)
$con = @mysqli_connect($hostname, $user, $password, $database);
if (!$con) {
    error_log("Failed to connect to primary database ($database): " . mysqli_connect_error());
} else {
    mysqli_set_charset($con, "utf8mb4");
}

// 2. Secondary Database Connection: billing_core (Master Catalog & WA Notification Queue)
$con2 = @mysqli_connect($hostname, $user, $password, $database2);
if (!$con2) {
    error_log("Failed to connect to billing_core database: " . mysqli_connect_error());
} else {
    mysqli_set_charset($con2, "utf8mb4");
}

// Host & Protocol Resolution (Robust for Reverse Proxy, Nginx, Cloudflare, SSL)
$isHttps = (
    (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' && $_SERVER['HTTPS'] != 0) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') ||
    (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)
);
$protocol = $isHttps ? "https://" : "http://";

if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
} else if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
} else {
    $host = 'localhost';
}

// Remove standard port artifacts (:80 and :443) from hostname
$host = preg_replace('/:(80|443)$/', '', $host);

$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir = str_replace('\\', '/', $scriptDir);
$scriptDir = trim($scriptDir, '/');

if (!empty($scriptDir)) {
    $basepath = $protocol . $host . '/' . $scriptDir . '/';
} else {
    $basepath = $protocol . $host . '/';
}

require_once 'db_lib.php';
