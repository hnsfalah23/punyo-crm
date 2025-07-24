<?php
// app/helpers/url_helper.php

/**
 * Memeriksa apakah URL menu saat ini cocok dengan URL halaman yang sedang dibuka.
 * @param string $url URL menu dari database.
 * @return string 'active' jika cocok, jika tidak string kosong.
 */
function isActive($url)
{
  // Ambil path URL saat ini, default ke 'dashboard' jika kosong
  $currentUrl = $_GET['url'] ?? 'dashboard';

  // Cek apakah URL menu adalah bagian dari URL saat ini
  // Ini berguna agar 'deals/add' tetap mengaktifkan menu 'deals'
  if (strpos($currentUrl, $url) === 0) {
    return 'active';
  }

  return '';
}
