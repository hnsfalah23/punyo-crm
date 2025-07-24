<?php
// app/helpers/auth_helper.php

/**
 * Memeriksa apakah pengguna sudah login.
 * Jika tidak, redirect ke halaman login.
 */
function isLoggedIn()
{
  if (isset($_SESSION['user_id'])) {
    return true;
  } else {
    return false;
  }
}

/**
 * Memeriksa izin CRUD untuk menu tertentu.
 *
 * @param string $permission Tipe izin ('create', 'read', 'update', 'delete').
 * @param string $menu_url URL menu (e.g., 'products').
 * @return bool True jika diizinkan, false jika tidak.
 */
function can($permission, $menu_url)
{
  // Admin (role_id 1) selalu memiliki semua hak akses
  if (isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 1) {
    return true;
  }

  if (isset($_SESSION['permissions'][$menu_url])) {
    $permissionKey = 'can_' . $permission;
    return $_SESSION['permissions'][$menu_url]->$permissionKey == 1;
  }
  return false;
}