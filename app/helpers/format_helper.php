<?php
// app/helpers/format_helper.php

/**
 * Mengubah format nomor telepon menjadi link WhatsApp (wa.me).
 * Menghapus karakter non-numerik dan mengganti '0' di depan dengan '62'.
 * @param string $phone Nomor telepon.
 * @return string URL WhatsApp.
 */
function format_wa_number($phone)
{
  if (empty($phone)) {
    return '#';
  }
  // Hapus semua karakter selain angka
  $phone = preg_replace('/[^0-9]/', '', $phone);
  // Jika angka pertama adalah 0, ganti dengan 62
  if (substr($phone, 0, 1) == '0') {
    $phone = '62' . substr($phone, 1);
  }
  return 'https://wa.me/' . $phone;
}
