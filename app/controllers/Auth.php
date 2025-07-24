<?php
// =============================================
// File: app/controllers/Auth.php
// =============================================

class Auth extends Controller
{
  private $userModel;
  private $permissionModel;

  public function __construct()
  {
    $this->userModel = $this->model('User');
    $this->permissionModel = $this->model('PermissionModel');
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
    $data = [
      'title' => 'Login Punyo CRM',
      'email' => trim($_POST['email']),
      'password' => trim($_POST['password']),
      'error' => ''
    ];

    if (empty($data['email']) || empty($data['password'])) {
      $data['error'] = 'Email dan password tidak boleh kosong.';
      $this->renderLoginPage($data);
      return;
    }

    if ($loggedInUser = $this->userModel->findUserByEmail($data['email'])) {
      if (password_verify($data['password'], $loggedInUser->password)) {
        $this->createUserSession($loggedInUser);
      } else {
        $data['error'] = 'Password salah.';
        $this->renderLoginPage($data);
      }
    } else {
      $data['error'] = 'Email tidak terdaftar.';
      $this->renderLoginPage($data);
    }
  }

  public function register()
  {
    if (isLoggedIn()) {
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'confirm_password' => trim($_POST['confirm_password']),
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];

      if (empty($data['name'])) $data['name_err'] = 'Nama tidak boleh kosong.';
      if (empty($data['email'])) $data['email_err'] = 'Email tidak boleh kosong.';
      elseif ($this->userModel->findUserByEmail($data['email'])) $data['email_err'] = 'Email sudah terdaftar.';
      if (empty($data['password'])) $data['password_err'] = 'Password tidak boleh kosong.';
      elseif (strlen($data['password']) < 6) $data['password_err'] = 'Password minimal 6 karakter.';
      if (empty($data['confirm_password'])) $data['confirm_password_err'] = 'Konfirmasi password tidak boleh kosong.';
      elseif ($data['password'] != $data['confirm_password']) $data['confirm_password_err'] = 'Password tidak cocok.';

      if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['role_id'] = 6; // Default Staf Marketing

        if ($this->userModel->addUser($data)) {
          flash('register_success', 'Registrasi berhasil! Silakan login.');
          header('Location: ' . BASE_URL . '/auth/login');
          exit;
        }
      } else {
        $this->renderRegisterPage($data);
      }
    } else {
      $data = [
        'title' => 'Register Punyo CRM',
        'name' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => '',
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];
      $this->renderRegisterPage($data);
    }
  }

  public function createUserSession($user)
  {
    $_SESSION['user_id'] = $user->user_id;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['user_name'] = $user->name;
    $_SESSION['user_role_id'] = $user->role_id;

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
    unset($_SESSION['allowed_menus']);
    unset($_SESSION['permissions']);
    session_destroy();
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
  }

  private function renderLoginPage($data)
  {
    $this->view('layouts/header', $data);
    $this->view('pages/login', $data);
    $this->view('layouts/footer');
  }

  private function renderRegisterPage($data)
  {
    $this->view('layouts/header', $data);
    $this->view('pages/register', $data);
    $this->view('layouts/footer');
  }
}
