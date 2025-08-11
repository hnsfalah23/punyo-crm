<?php
// app/controllers/Auth.php

class Auth extends Controller
{
  private $userModel;
  private $permissionModel;

  public function __construct()
  {
    // Tunda inisialisasi model hingga diperlukan agar halaman login GET tidak butuh DB
  }

  public function login()
  {
    if (isLoggedIn()) {
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $this->processLogin();
    } else {
      $data = [
        'title' => 'Login Punyo CRM',
        'error' => ''
      ];
      $this->view('layouts/header', $data);
      $this->view('pages/login', $data);
      $this->view('layouts/footer');
    }
  }

  public function processLogin()
  {
    // Inisialisasi model saat diperlukan
    $this->userModel = $this->model('User');
    $this->permissionModel = $this->model('PermissionModel');

    $data = [
      'title' => 'Login Punyo CRM',
      'email' => trim($_POST['email'] ?? ''),
      'password' => trim($_POST['password'] ?? ''),
      'error' => ''
    ];

    if (empty($data['email']) || empty($data['password'])) {
      $data['error'] = 'Email dan password tidak boleh kosong.';
      $this->view('layouts/header', $data);
      $this->view('pages/login', $data);
      $this->view('layouts/footer');
      return;
    }

    try {
      $user = $this->userModel->login($data['email'], $data['password']);
    } catch (Exception $e) {
      $data['error'] = 'Terjadi kesalahan koneksi database. Coba lagi nanti.';
      $this->view('layouts/header', $data);
      $this->view('pages/login', $data);
      $this->view('layouts/footer');
      return;
    }

    if ($user) {
      $this->createUserSession($user);
    } else {
      $data['error'] = 'Email atau password salah.';
      $this->view('layouts/header', $data);
      $this->view('pages/login', $data);
      $this->view('layouts/footer');
    }
  }

  public function createUserSession($user)
  {
    $_SESSION['user_id'] = $user->user_id;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['user_name'] = $user->name;
    $_SESSION['user_role_id'] = $user->role_id;
    $_SESSION['user_photo'] = $user->profile_picture;

    // Pastikan permissionModel tersedia
    if (!$this->permissionModel) {
      $this->permissionModel = $this->model('PermissionModel');
    }

    $permissions = $this->permissionModel->getPermissionsByRoleId($user->role_id);
    $allMenus = $this->permissionModel->getAllMenus();

    $allowedMenus = [];
    $permissionMap = [];
    foreach ($allMenus as $menu) {
      if (isset($permissions[$menu->menu_id]) && $permissions[$menu->menu_id]->can_read) {
        $allowedMenus[] = $menu;
        $permissionMap[$menu->menu_url] = $permissions[$menu->menu_id];
      }
    }
    $_SESSION['allowed_menus'] = $allowedMenus;
    $_SESSION['permissions'] = $permissionMap;

    header('Location: ' . BASE_URL . '/dashboard');
    exit;
  }

  public function logout()
  {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_role_id']);
    unset($_SESSION['user_photo']);
    unset($_SESSION['allowed_menus']);
    unset($_SESSION['permissions']);
    session_destroy();
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
  }
}
