<?php
// public/index.php

// Mulai session
session_start();

// PERBAIKAN 1: Muat file inisialisasi utama aplikasi Anda.
// Ganti 'init.php' jika nama file Anda berbeda (misalnya, 'core.php', 'loader.php', dll.).
// File ini seharusnya yang memuat semua file inti seperti Router.php, Controller.php, dll.
require_once '../app/init.php';

// PERBAIKAN 2: Tambahkan definisi konstanta ini.
// Ini akan membuat path absolut ke folder 'public' Anda.
define('PUBLIC_ROOT', dirname(__FILE__));

// Inisialisasi Core Library (Router)
$init = new Router();
