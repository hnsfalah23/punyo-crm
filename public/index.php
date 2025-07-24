<?php
// public/index.php

// Mulai session
if (!session_id()) {
  session_start();
}

// Definisikan konstanta ROOT untuk path absolut ke folder proyek
define('ROOT', dirname(__DIR__));

// Panggil file init (yang akan memuat semua file core)
require_once ROOT . '/app/init.php';

// Jalankan router
$router = new Router();
