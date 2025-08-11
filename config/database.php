<?php
// config/database.php

// Pengaturan koneksi database (bisa dioverride via ENV)
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: 3306);
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'punyo-crm');

// Pengaturan dasar aplikasi (dinamis berdasarkan request)
if (!defined('BASE_URL')) {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $basePath = rtrim(str_replace('public', '', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
  define('BASE_URL', $scheme . '://' . $host . $basePath);
}
