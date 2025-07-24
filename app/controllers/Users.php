<?php
// app/controllers/Users.php

class Users extends Controller
{
  private $userModel;

  public function __construct()
  {
    // Pengecekan baru yang dinamis
    if (!isLoggedIn()) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    if (!can('read', 'users')) {
      flash('dashboard_message', 'Anda tidak memiliki hak akses ke halaman tersebut.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }
    $this->userModel = $this->model('User');
  }
  
  public function index()
  {
    if (!can('read', 'users')) {
      flash('user_message', 'Anda tidak memiliki izin untuk melihat pengguna.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }
    $users = $this->userModel->getAllUsersWithRoles();
    $data = [
      'title' => 'Manajemen Pengguna',
      'users' => $users
    ];
    $this->renderView('pages/users/index', $data);
  }

  public function add()
  {
    if (!can('create', 'users')) {
      flash('user_message', 'Anda tidak memiliki izin untuk menambah pengguna.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/users');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Proses form
      $data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'confirm_password' => trim($_POST['confirm_password']),
        'role_id' => $_POST['role_id'],
        'roles' => $this->userModel->getAllRoles(),
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => '',
        'role_id_err' => ''
      ];

      // Validasi (logika validasi tetap sama)
      if (empty($data['name'])) $data['name_err'] = 'Nama tidak boleh kosong.';
      if (empty($data['email'])) $data['email_err'] = 'Email tidak boleh kosong.';
      elseif ($this->userModel->findUserByEmail($data['email'])) $data['email_err'] = 'Email sudah terdaftar.';
      if (empty($data['role_id'])) $data['role_id_err'] = 'Peran harus dipilih.';
      if (empty($data['password'])) $data['password_err'] = 'Password tidak boleh kosong.';
      elseif (strlen($data['password']) < 6) $data['password_err'] = 'Password minimal 6 karakter.';
      if (empty($data['confirm_password'])) $data['confirm_password_err'] = 'Konfirmasi password tidak boleh kosong.';
      elseif ($data['password'] != $data['confirm_password']) $data['confirm_password_err'] = 'Password tidak cocok.';

      if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err']) && empty($data['role_id_err'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        if ($this->userModel->addUser($data)) {
          flash('user_message', 'Pengguna baru berhasil ditambahkan.', 'success');
          header('Location: ' . BASE_URL . '/users');
          exit;
        } else {
          die('Terjadi kesalahan.');
        }
      } else {
        $data['title'] = 'Tambah Pengguna';
        $this->renderView('pages/users/add', $data);
      }
    } else {
      $data = [
        'title' => 'Tambah Pengguna',
        'name' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => '',
        'role_id' => '',
        'roles' => $this->userModel->getAllRoles(),
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => '',
        'role_id_err' => ''
      ];
      $this->renderView('pages/users/add', $data);
    }
  }

  public function edit($id)
  {
    if (!can('update', 'users')) {
      flash('user_message', 'Anda tidak memiliki izin untuk mengedit pengguna.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/users');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Logika proses edit tetap sama
      $user = $this->userModel->getUserById($id);
      $data = [
        'id' => $id,
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'role_id' => $_POST['role_id'],
        'roles' => $this->userModel->getAllRoles(),
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'role_id_err' => ''
      ];

      if (empty($data['name'])) $data['name_err'] = 'Nama tidak boleh kosong.';
      if (empty($data['email'])) $data['email_err'] = 'Email tidak boleh kosong.';
      elseif ($this->userModel->findUserByEmail($data['email']) && $data['email'] != $user->email) $data['email_err'] = 'Email sudah terdaftar.';
      if (empty($data['role_id'])) $data['role_id_err'] = 'Peran harus dipilih.';
      if (!empty($data['password']) && strlen($data['password']) < 6) $data['password_err'] = 'Password minimal 6 karakter.';

      if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['role_id_err'])) {
        if (!empty($data['password'])) $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        if ($this->userModel->updateUser($data)) {
          flash('user_message', 'Data pengguna berhasil diupdate.', 'success');
          header('Location: ' . BASE_URL . '/users');
          exit;
        } else {
          die('Terjadi kesalahan.');
        }
      } else {
        $data['title'] = 'Edit Pengguna';
        $this->renderView('pages/users/edit', $data);
      }
    } else {
      // Logika menampilkan form edit tetap sama
      $user = $this->userModel->getUserById($id);
      if (!$user || $user->user_id == $_SESSION['user_id']) {
        header('Location: ' . BASE_URL . '/users');
        exit;
      }
      $data = [
        'title' => 'Edit Pengguna',
        'id' => $id,
        'name' => $user->name,
        'email' => $user->email,
        'role_id' => $user->role_id,
        'roles' => $this->userModel->getAllRoles(),
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'role_id_err' => ''
      ];
      $this->renderView('pages/users/edit', $data);
    }
  }

  public function delete($id)
  {
    if (!can('delete', 'users')) {
      flash('user_message', 'Anda tidak memiliki izin untuk menghapus pengguna.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/users');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($id == $_SESSION['user_id']) {
        flash('user_message', 'Anda tidak dapat menghapus akun Anda sendiri.', 'error');
        header('Location: ' . BASE_URL . '/users');
        exit;
      }

      if ($this->userModel->deleteUser($id)) {
        flash('user_message', 'Pengguna berhasil dihapus.', 'success');
        header('Location: ' . BASE_URL . '/users');
        exit;
      } else {
        die('Terjadi kesalahan.');
      }
    } else {
      header('Location: ' . BASE_URL . '/users');
      exit;
    }
  }

  private function renderView($view, $data = [])
  {
    $this->view('layouts/header', $data);
    $this->view('layouts/sidebar', $data);
    echo '<div id="page-content-wrapper">';
    $this->view('layouts/topbar', $data);
    $this->view($view, $data);
    echo '</div>';
    $this->view('layouts/footer', $data);
  }
}
