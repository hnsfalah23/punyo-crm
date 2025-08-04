<?php
// app/controllers/Activities.php

class Activities extends Controller
{
  private $activityModel;
  private $userModel;

  public function __construct()
  {
    if (!isLoggedIn()) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    $this->activityModel = $this->model('ActivityModel');
    $this->userModel = $this->model('User');
  }

  private function getUserScope()
  {
    $scope_type = 'all';
    $scope_value = null;
    $user_role_id = $_SESSION['user_role_id'];
    $user_id = $_SESSION['user_id'];
    $currentUser = $this->userModel->getUserById($user_id);
    $user_division_id = $currentUser->division_id ?? null;
    if (in_array($user_role_id, [3, 4, 5])) {
      $scope_type = 'division';
      $scope_value = $user_division_id;
    } elseif ($user_role_id == 6) {
      $scope_type = 'self';
      $scope_value = $user_id;
    }
    return ['type' => $scope_type, 'value' => $scope_value];
  }

  public function index()
  {
    if (!can('read', 'activities')) {
      flash('dashboard_message', 'Anda tidak memiliki hak akses.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }
    $scope = $this->getUserScope();
    $params = [
      'scope_type' => $scope['type'],
      'scope_value' => $scope['value']
    ];
    $activities = $this->activityModel->getAllActivitiesDetails($params);
    $data = [
      'title' => 'Semua Aktivitas',
      'activities' => $activities
    ];
    $this->renderView('pages/activities/index', $data);
  }

  public function add()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !can('create', 'activities')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    $startTime = !empty($_POST['start_date']) && !empty($_POST['start_time']) ? $_POST['start_date'] . ' ' . $_POST['start_time'] : null;
    $endTime = !empty($_POST['end_date']) && !empty($_POST['end_time']) ? $_POST['end_date'] . ' ' . $_POST['end_time'] : null;

    $data = [
      'name' => trim($_POST['name'] ?? ''),
      'type' => $_POST['type'] ?? 'Tugas',
      'description' => trim($_POST['description'] ?? ''),
      'start_time' => $startTime,
      'end_time' => $endTime,
      'owner_id' => $_SESSION['user_id'],
      'related_item_id' => $_POST['related_item_id'] ?? 0,
      'related_item_type' => $_POST['related_item_type'] ?? '',
      'documentation_photo' => $_FILES['documentation_photo'] ?? null
    ];

    if (empty($data['name']) || empty($data['related_item_id'])) {
      $this->jsonResponse(false, 'Nama aktivitas dan item terkait harus diisi.');
      return;
    }

    try {
      if ($this->activityModel->addActivity($data)) {
        $this->jsonResponse(true, 'Aktivitas baru berhasil ditambahkan.');
      } else {
        $this->jsonResponse(false, 'Gagal menambahkan aktivitas ke database.');
      }
    } catch (Exception $e) {
      $this->jsonResponse(false, 'Terjadi kesalahan pada server: ' . $e->getMessage(), 500);
    }
  }
  
  public function edit($id)
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !can('update', 'activities')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    $startTime = $_POST['start_date'] . ' ' . $_POST['start_time'];
    $endTime = (!empty($_POST['end_date']) && !empty($_POST['end_time'])) ? $_POST['end_date'] . ' ' . $_POST['end_time'] : null;

    $data = [
      'id' => $id,
      'name' => trim($_POST['name']),
      'type' => $_POST['type'],
      'description' => trim($_POST['description']),
      'start_time' => $startTime,
      'end_time' => $endTime,
      'documentation_photo' => $_FILES['documentation_photo'] ?? null
    ];

    try {
      if ($this->activityModel->updateActivity($data)) {
        $this->jsonResponse(true, 'Aktivitas berhasil diperbarui.');
      } else {
        $this->jsonResponse(false, 'Gagal memperbarui aktivitas.');
      }
    } catch (Exception $e) {
      $this->jsonResponse(false, 'Terjadi kesalahan pada server: ' . $e->getMessage(), 500);
    }
  }

  public function delete($id)
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !can('delete', 'activities')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    try {
      if ($this->activityModel->deleteActivity($id)) {
        $this->jsonResponse(true, 'Aktivitas berhasil dihapus.');
      } else {
        $this->jsonResponse(false, 'Gagal menghapus aktivitas.');
      }
    } catch (Exception $e) {
      $this->jsonResponse(false, 'Terjadi kesalahan pada server: ' . $e->getMessage(), 500);
    }
  }

  public function getActivityJson($id)
  {
    if (!can('read', 'activities')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }
    $activity = $this->activityModel->getActivityById($id);
    if ($activity) {
      $this->jsonResponse(true, 'Data ditemukan', 200, (array)$activity);
    } else {
      $this->jsonResponse(false, 'Aktivitas tidak ditemukan', 404);
    }
  }

  private function jsonResponse($success, $message, $httpCode = 200, $data = [])
  {
    if (ob_get_level() > 0) {
      ob_end_clean();
    }
    header('Content-Type: application/json');
    http_response_code($httpCode);
    $response = ['success' => $success, 'message' => $message, 'data' => $data];
    echo json_encode($response);
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
