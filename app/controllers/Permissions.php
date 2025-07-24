<?php
// app/controllers/Permissions.php

class Permissions extends Controller
{
  private $permissionModel;
  private $userModel;

  public function __construct()
  {
    if (!isLoggedIn()) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    // Hanya Admin (role_id 1) yang bisa mengakses halaman ini
    if ($_SESSION['user_role_id'] != 1) {
      flash('dashboard_message', 'Anda tidak memiliki hak akses ke halaman tersebut.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }
    $this->permissionModel = $this->model('PermissionModel');
    $this->userModel = $this->model('User');
  }

  public function index()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $role_id = $_POST['role_id'];
      $permissions = $_POST['permissions'] ?? [];

      if ($this->permissionModel->updatePermissions($role_id, $permissions)) {
        flash('permission_message', 'Hak akses berhasil diperbarui.');
      } else {
        flash('permission_message', 'Gagal memperbarui hak akses.', 'error');
      }
      // Redirect kembali ke halaman dengan role yang sama
      header('Location: ' . BASE_URL . '/permissions?role=' . $role_id);
      exit;
    } else {
      $selected_role = $_GET['role'] ?? 1; // Default Admin
      $data = [
        'title' => 'Manajemen Hak Akses',
        'roles' => $this->userModel->getAllRoles(),
        'menus' => $this->permissionModel->getAllMenus(),
        'permissions' => $this->permissionModel->getPermissionsByRoleId($selected_role),
        'selected_role_id' => $selected_role
      ];
      $this->renderView('pages/permissions/index', $data);
    }
  }

  public function getByRole($role_id)
  {
    header('Content-Type: application/json');
    $permissions = $this->permissionModel->getPermissionsByRoleId($role_id);
    echo json_encode($permissions);
    exit;
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
