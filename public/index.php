<?php
// public/index.php

// Mulai session lebih awal
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Muat file inisialisasi utama aplikasi
require_once dirname(__DIR__) . '/app/init.php';

// Definisikan konstanta path public
if (!defined('PUBLIC_ROOT')) {
  define('PUBLIC_ROOT', __DIR__);
}

// Normalisasi URL untuk router (agar bekerja tanpa .htaccess)
if (!isset($_GET['url'])) {
  $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
  $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
  if ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
    $path = substr($path, strlen($scriptDir));
  }
  $_GET['url'] = trim($path, '/');
}

// Jalankan router
$init = new Router();
