<?php
// app/init.php

date_default_timezone_set('Asia/Jakarta');

// PERBAIKAN: Definisikan konstanta path inti di sini
define('APPROOT', dirname(__FILE__)); // Path ke folder 'app'
define('ROOT', dirname(APPROOT));     // Path ke folder root proyek (punyo-crm)

// Muat file konfigurasi (sekarang bisa menggunakan ROOT)
require_once ROOT . '/config/database.php';

// Muat file-file core
require_once APPROOT . '/core/Router.php';
require_once APPROOT . '/core/Controller.php';
require_once APPROOT . '/core/Database.php';

// Muat helper
require_once APPROOT . '/helpers/session_helper.php';
require_once APPROOT . '/helpers/auth_helper.php';
require_once APPROOT . '/helpers/url_helper.php'; // Pastikan baris ini ada
require_once APPROOT . '/helpers/format_helper.php';
