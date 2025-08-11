<?php
// app/init.php

date_default_timezone_set('Asia/Jakarta');

// Definisikan konstanta path inti
if (!defined('APPROOT')) {
  define('APPROOT', __DIR__); // Path ke folder 'app'
}
if (!defined('ROOT')) {
  define('ROOT', dirname(APPROOT)); // Path ke root proyek
}

// Muat file konfigurasi
require_once ROOT . '/config/database.php';

// Muat file-file core
require_once APPROOT . '/core/Router.php';
require_once APPROOT . '/core/Controller.php';
require_once APPROOT . '/core/Database.php';

// Muat helper
require_once APPROOT . '/helpers/session_helper.php';
require_once APPROOT . '/helpers/auth_helper.php';
require_once APPROOT . '/helpers/url_helper.php';
require_once APPROOT . '/helpers/format_helper.php';
