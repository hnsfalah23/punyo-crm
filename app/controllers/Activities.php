<?php
// app/controllers/Activities.php
class Activities extends Controller
{
  private $activityModel;
  private $userModel;
  private $prospekModel; // Untuk mengambil data prospek
  private $peluangModel; // Untuk mengambil data peluang

  public function __construct()
  {
    if (!isLoggedIn()) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    $this->activityModel = $this->model('ActivityModel');
    $this->userModel = $this->model('User');
    $this->prospekModel = $this->model('ProspekModel');
    $this->peluangModel = $this->model('PeluangModel');

  }

  /**
   * Method untuk menampilkan halaman utama daftar aktivitas.
   * Ini adalah method yang hilang dan menyebabkan halaman kosong.
   */
  public function index()
  {
    $user_id = null;
    // Jika role_id adalah 3 (Staf Marketing), maka set user_id
    if (isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 6) {
      $user_id = $_SESSION['user_id'];
    }

    $data = [
      'title' => 'Manajemen Aktivitas',
      'upcoming_activities' => $this->activityModel->getUpcomingActivities($user_id),
      'past_activities' => $this->activityModel->getPastActivities($user_id),
      'prospek' => $this->prospekModel->getProspek(),
      'peluang' => $this->peluangModel->getPeluang()
    ];
    $this->renderView('pages/activities/index', $data);
  }


  public function add()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Logika validasi dan sanitasi input
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // --- AWAL LOGIKA UPLOAD FOTO ---
      $photo_name = null;
      if (isset($_FILES['documentation_photo']) && $_FILES['documentation_photo']['error'] == 0) {
        $target_dir = "uploads/activities/";
        // Buat nama file unik
        $photo_name = uniqid() . '_' . basename($_FILES["documentation_photo"]["name"]);
        $target_file = $target_dir . $photo_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Cek tipe file (opsional, tapi disarankan)
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
          // Pindahkan file ke folder tujuan
          if (!move_uploaded_file($_FILES["documentation_photo"]["tmp_name"], $target_file)) {
            $photo_name = null; // Gagal upload, set nama foto jadi null
          }
        } else {
          $photo_name = null; // Tipe file tidak diizinkan
        }
      }
      // --- AKHIR LOGIKA UPLOAD FOTO ---

      // Menentukan item terkait (prospek atau peluang)
      $related_item_id = 0;
      $related_item_type = '';
      if (!empty($_POST['prospek_id'])) {
        $related_item_id = $_POST['prospek_id'];
        $related_item_type = 'lead';
      } elseif (!empty($_POST['peluang_id'])) {
        $related_item_id = $_POST['peluang_id'];
        $related_item_type = 'deal';
      }

      $data = [
        'name' => trim($_POST['name']),
        'type' => trim($_POST['type']),
        'description' => trim($_POST['description']),
        'start_time' => trim($_POST['start_time']),
        'end_time' => trim($_POST['end_time']),
        'related_item_id' => $related_item_id,
        'related_item_type' => $related_item_type,
        'documentation_photo' => $photo_name // Gunakan nama file yang sudah diproses
      ];

      if ($this->activityModel->addActivity($data)) {
        flash('activity_message', 'Aktivitas berhasil ditambahkan.');
        header('Location: ' . BASE_URL . '/activities');
      } else {
        flash('activity_message', 'Gagal menambahkan aktivitas.', 'alert alert-danger');
        header('Location: ' . BASE_URL . '/activities');
      }
    } else {
      header('Location: ' . BASE_URL . '/activities');
    }
  }

  public function edit($id)
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !can('update', 'activities')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    try {
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

      if ($this->activityModel->updateActivity($data)) {
        $this->jsonResponse(true, 'Aktivitas berhasil diperbarui.');
      } else {
        $this->jsonResponse(false, 'Gagal memperbarui aktivitas.');
      }
    } catch (Exception $e) {
      $this->jsonResponse(false, 'Terjadi kesalahan pada server.', 500);
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
      $this->jsonResponse(false, 'Terjadi kesalahan pada server.', 500);
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
    if (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/json');
    http_response_code($httpCode);
    $response = ['success' => $success, 'message' => $message, 'data' => $data];
    echo json_encode($response);
    exit;
  }

  public function calendar_json()
  {
    // Set header untuk JSON response
    header('Content-Type: application/json');

    try {
      $user_id = null;
      // Jika role_id adalah 3 (Staf Marketing), maka set user_id
      if (isset($_SESSION['user_role_id']) && $_SESSION['user_role_id'] == 6) {
        $user_id = $_SESSION['user_id'];
      }

      // Ambil semua activities dari database, dengan filter user_id jika ada
      $activities = $this->activityModel->getAllActivitiesForCalendar($user_id);

      $events = [];

      foreach ($activities as $activity) {
        $events[] = [
          'id' => $activity->activity_id ?? $activity->id,
          'title' => $activity->name,
          'start' => $activity->start_time,
          'end' => $activity->end_time ?? null,
          'description' => $activity->description ?? '',
          'backgroundColor' => $this->getEventColor($activity->type),
          'borderColor' => $this->getEventColor($activity->type),
          'extendedProps' => [
            'type' => $activity->type,
            'owner' => $activity->owner_name ?? 'Unknown',
            'related_to' => $activity->related_item_type ?? null
          ]
        ];
      }

      echo json_encode($events);
    } catch (Exception $e) {
      // Log error
      error_log('Calendar JSON Error: ' . $e->getMessage());

      // Return empty array on error
      echo json_encode([]);
    }
  }

  // Helper method untuk memberikan warna berbeda berdasarkan tipe aktivitas
  private function getEventColor($type)
  {
    $colors = [
      'Panggilan' => '#28a745', // Hijau
      'Email' => '#007bff',     // Biru
      'Rapat' => '#ffc107',     // Kuning
      'Tugas' => '#dc3545'      // Merah
    ];

    return $colors[$type] ?? '#6c757d'; // Default abu-abu
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
