<?php
// app/controllers/Activities.php

class Activities extends Controller
{
  private $activityModel;

  public function __construct()
  {
    if (!isset($_SESSION['user_id'])) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    $this->activityModel = $this->model('ActivityModel');
  }

  public function add()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $photoName = null;
      if (isset($_FILES['documentation_photo']) && $_FILES['documentation_photo']['error'] == 0) {
        $targetDir = "uploads/activities/";
        $fileName = basename($_FILES["documentation_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $photoName = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDir . $photoName;
        if (getimagesize($_FILES["documentation_photo"]["tmp_name"])) {
          move_uploaded_file($_FILES["documentation_photo"]["tmp_name"], $targetFile);
        } else {
          $photoName = null;
        }
      }

      $startTime = $_POST['start_date'] . ' ' . $_POST['start_time'] . ':00';
      $endTime = !empty($_POST['end_date']) ? $_POST['end_date'] . ' ' . $_POST['end_time'] . ':00' : null;

      $data = [
        'name' => trim($_POST['name']),
        'type' => $_POST['type'],
        'description' => trim($_POST['description']),
        'start_time' => $startTime,
        'end_time' => $endTime,
        'owner_id' => $_SESSION['user_id'],
        'related_item_id' => $_POST['related_item_id'],
        'related_item_type' => $_POST['related_item_type'],
        'redirect_url' => $_POST['redirect_url'],
        'documentation_photo' => $photoName
      ];

      if (!empty($data['name']) && !empty($data['type'])) {
        if ($this->activityModel->addActivity($data)) {
          flash('activity_message', 'Aktivitas baru berhasil dicatat.');
        } else {
          flash('activity_message', 'Gagal mencatat aktivitas.', 'error');
        }
      } else {
        flash('activity_message', 'Nama dan jenis aktivitas tidak boleh kosong.', 'error');
      }
      header('Location: ' . $data['redirect_url']);
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
        $fileName = basename($_FILES["documentation_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $photoName = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDir . $photoName;
        move_uploaded_file($_FILES["documentation_photo"]["tmp_name"], $targetFile);
      }

      $startTime = $_POST['start_date'] . ' ' . $_POST['start_time'] . ':00';
      $endTime = !empty($_POST['end_date']) ? $_POST['end_date'] . ' ' . $_POST['end_time'] . ':00' : null;

      $data = [
        'id' => $id,
        'name' => trim($_POST['name']),
        'type' => $_POST['type'],
        'description' => trim($_POST['description']),
        'start_time' => $startTime,
        'end_time' => $endTime,
        'documentation_photo' => $photoName,
        'redirect_url' => $_POST['redirect_url']
      ];

      if ($this->activityModel->updateActivity($data)) {
        flash('activity_message', 'Aktivitas berhasil diupdate.');
      } else {
        flash('activity_message', 'Gagal mengupdate aktivitas.', 'error');
      }
      header('Location: ' . $data['redirect_url']);
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
          flash('activity_message', 'Gagal menghapus aktivitas.', 'error');
        }
      }
      $redirect_url = $_POST['redirect_url'] ?? BASE_URL . '/deals';
      header('Location: ' . $redirect_url);
      exit;
    }
  }
}
