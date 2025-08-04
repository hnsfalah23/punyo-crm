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
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $itemType = $_POST['related_item_type'];
      $itemId = $_POST['related_item_id'];
      $permissionType = ($itemType == 'deal') ? 'deals' : 'leads';

      if (!can('create', $permissionType)) {
        flash('activity_message', 'Anda tidak memiliki izin untuk menambahkan aktivitas ini.', 'alert alert-danger');
        header('Location: ' . $_POST['redirect_url']);
        exit;
      }

      $photoName = null;
      if (isset($_FILES['documentation_photo']) && $_FILES['documentation_photo']['error'] == 0) {
        $targetDir = "uploads/activities/";
        if (!file_exists($targetDir)) {
          mkdir($targetDir, 0777, true);
        }
        $fileName = uniqid() . '_' . basename($_FILES["documentation_photo"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["documentation_photo"]["tmp_name"], $targetFile)) {
          $photoName = $fileName;
        }
      }

      $startTime = $_POST['start_date'] . ' ' . $_POST['start_time'];
      $endTime = (!empty($_POST['end_date']) && !empty($_POST['end_time'])) ? $_POST['end_date'] . ' ' . $_POST['end_time'] : null;

      $data = [
        'name' => trim($_POST['name']),
        'type' => $_POST['type'],
        'description' => trim($_POST['description']),
        'start_time' => $startTime,
        'end_time' => $endTime,
        'owner_id' => $_SESSION['user_id'],
        'related_item_id' => $itemId,
        'related_item_type' => $itemType,
        'documentation_photo' => $photoName
      ];

      if ($this->activityModel->addActivity($data)) {
        flash('activity_message', 'Aktivitas baru berhasil dicatat.');
      } else {
        flash('activity_message', 'Gagal mencatat aktivitas.', 'alert alert-danger');
      }

      // **PERBAIKAN UTAMA DI SINI:** Alihkan ke halaman detail yang sesuai
      $redirect_url = BASE_URL . '/' . $permissionType . '/detail/' . $itemId;
      header('Location: ' . $redirect_url);
      exit;
    }
  }

  public function edit($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $activity = $this->activityModel->getActivityById($id);
      $photoName = $activity->documentation_photo;

      if (isset($_FILES['documentation_photo']) && $_FILES['documentation_photo']['error'] == 0) {
        if ($photoName && file_exists('uploads/activities/' . $photoName)) {
          unlink('uploads/activities/' . $photoName);
        }
        $targetDir = "uploads/activities/";
        $fileName = uniqid() . '_' . basename($_FILES["documentation_photo"]["name"]);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["documentation_photo"]["tmp_name"], $targetFile);
        $photoName = $fileName;
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
        'documentation_photo' => $photoName
      ];

      if ($this->activityModel->updateActivity($data)) {
        flash('activity_message', 'Aktivitas berhasil diupdate.');
      } else {
        flash('activity_message', 'Gagal mengupdate aktivitas.', 'alert alert-danger');
      }
      header('Location: ' . $_POST['redirect_url']);
      exit;
    }
  }

  public function delete($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $activity = $this->activityModel->getActivityById($id);
      if ($activity) {
        if ($activity->documentation_photo && file_exists('uploads/activities/' . $activity->documentation_photo)) {
          unlink('uploads/activities/' . $activity->documentation_photo);
        }
        if ($this->activityModel->deleteActivity($id)) {
          flash('activity_message', 'Aktivitas berhasil dihapus.');
        } else {
          flash('activity_message', 'Gagal menghapus aktivitas.', 'alert alert-danger');
        }
      }
      $redirect_url = $_POST['redirect_url'] ?? BASE_URL . '/dashboard';
      header('Location: ' . $redirect_url);
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
