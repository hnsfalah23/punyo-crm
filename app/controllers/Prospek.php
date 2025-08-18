<?php
// app/controllers/Prospek.php

class Prospek extends Controller
{
  private $prospekModel;
  private $instansiModel;
  private $kontakModel;
  private $peluangModel;
  private $userModel;
  private $activityModel;
  private $permissionModel;

  public function __construct()
  {
    if (!isLoggedIn()) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    $this->prospekModel = $this->model('ProspekModel');
    $this->instansiModel = $this->model('InstansiModel');
    $this->kontakModel = $this->model('KontakModel');
    $this->peluangModel = $this->model('PeluangModel');
    $this->userModel = $this->model('User');
    $this->activityModel = $this->model('ActivityModel');
    $this->permissionModel = $this->model('PermissionModel');

    // Cek hak akses untuk menu Prospek (menu_id = 2)
    $permissions = $this->permissionModel->getPermissionsByRoleId($_SESSION['user_role_id']);
    $menu_id = 2; // ID untuk menu 'Prospek'

    // Periksa apakah user memiliki hak akses 'can_read'
    // Logika ini dipindahkan ke masing-masing method untuk pengecekan yang lebih spesifik
  }

  private function getUserScope()
  {
    $scope_type = 'all';
    $scope_value = null;
    $user_role_id = $_SESSION['user_role_id'];
    $user_id = $_SESSION['user_id'];

    $currentUser = $this->userModel->getUserById($user_id);
    $user_division_id = $currentUser->division_id ?? null;

    if (in_array($user_role_id, [3, 4, 5])) { // Manajer, SPV
      $scope_type = 'division';
      $scope_value = $user_division_id;
    } elseif ($user_role_id == 6) { // Staf
      $scope_type = 'self';
      $scope_value = $user_id;
    }
    return ['type' => $scope_type, 'value' => $scope_value];
  }

  public function index()
  {
    if (!can('read', 'leads')) {
      flash('dashboard_message', 'Anda tidak memiliki hak akses.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }

    $scope = $this->getUserScope();
    $params = [
      'search' => $_GET['search'] ?? '',
      'status' => $_GET['status'] ?? '',
      'limit' => $_GET['limit'] ?? 10,
      'offset' => (($_GET['page'] ?? 1) - 1) * ($_GET['limit'] ?? 10),
      'scope_type' => $scope['type'],
      'scope_value' => $scope['value']
    ];

    $prospek = $this->prospekModel->getProspek($params);
    $totalProspek = $this->prospekModel->getTotalProspek($params);
    $totalPages = ($params['limit'] > 0) ? ceil($totalProspek / $params['limit']) : 1;

    $data = [
      'title' => 'Manajemen Prospek',
      'leads' => $prospek,
      'total_leads' => $totalProspek,
      'total_pages' => $totalPages,
      'current_page' => $_GET['page'] ?? 1,
      'limit' => $params['limit'],
      'search' => $params['search'],
      'status' => $params['status']
    ];
    $this->renderView('pages/prospek/index', $data);
  }

  public function detail($id)
  {
    if (!can('read', 'leads')) { // Gunakan 'leads' agar konsisten
      header('Location: ' . BASE_URL . '/prospek');
      exit;
    }
    $prospek = $this->prospekModel->getProspekById($id);
    if (!$prospek) {
      header('Location: ' . BASE_URL . '/prospek');
      exit;
    }
    $data = [
      'title' => 'Detail Prospek',
      'lead' => $prospek,
      'activities' => $this->activityModel->getActivitiesByItemId($id, 'lead')
    ];
    $this->renderView('pages/prospek/detail', $data);
  }

  public function add()
  {
    if (!can('create', 'leads') || $_SERVER['REQUEST_METHOD'] != 'POST') {
      header('Location: ' . BASE_URL . '/prospek');
      exit;
    }
    $data = [
      'name' => trim($_POST['name']),
      'status' => $_POST['status'],
      'source' => trim($_POST['source']),
      'company_name' => trim($_POST['company_name']),
      'email' => trim($_POST['email']),
      'phone' => trim($_POST['phone']),
      'owner_id' => $_SESSION['user_id']
    ];
    if (empty($data['name'])) {
      flash('lead_message', 'Nama prospek tidak boleh kosong.', 'alert alert-danger');
    } else {
      if ($this->prospekModel->addProspek($data)) {
        flash('lead_message', 'Prospek baru berhasil ditambahkan.');
      } else {
        flash('lead_message', 'Gagal menambahkan prospek.', 'alert alert-danger');
      }
    }
    header('Location: ' . BASE_URL . '/prospek');
    exit;
  }

  public function edit($id)
  {
    if (!can('update', 'leads') || $_SERVER['REQUEST_METHOD'] != 'POST') {
      $this->jsonResponse(false, 'Akses tidak diizinkan', 403);
    }

    $originalProspek = $this->prospekModel->getProspekById($id);
    if (!$originalProspek) {
      $this->jsonResponse(false, 'Prospek tidak ditemukan', 404);
    }

    $data = [
      'id' => $id,
      'name' => trim($_POST['name']),
      'status' => $_POST['status'],
      'source' => trim($_POST['source']),
      'company_name' => trim($_POST['company_name']),
      'email' => trim($_POST['email']),
      'phone' => trim($_POST['phone'])
    ];

    if (empty($data['name'])) {
      $this->jsonResponse(false, 'Nama prospek tidak boleh kosong.');
    }

    if ($this->prospekModel->updateProspek($data)) {
      if ($data['status'] == 'Kualifikasi' && $originalProspek->status != 'Kualifikasi') {
        $conversionResult = $this->convert($id, true);
        $this->jsonResponse($conversionResult['success'], $conversionResult['message'], 200, $conversionResult['data']);
      } else {
        $this->jsonResponse(true, 'Data prospek berhasil diperbarui.');
      }
    } else {
      $this->jsonResponse(false, 'Gagal mengupdate data prospek.');
    }
  }

  public function delete($id)
  {
    if (!can('delete', 'leads') || $_SERVER['REQUEST_METHOD'] != 'POST') {
      header('Location: ' . BASE_URL . '/prospek');
      exit;
    }
    if ($this->prospekModel->deleteProspek($id)) {
      flash('lead_message', 'Prospek berhasil dihapus.');
    } else {
      flash('lead_message', 'Gagal menghapus prospek.', 'alert alert-danger');
    }
    header('Location: ' . BASE_URL . '/prospek');
    exit;
  }

  public function getProspekJson($id)
  {
    header('Content-Type: application/json');
    if (!can('read', 'leads')) {
      echo json_encode(['error' => 'Akses ditolak']);
      exit;
    }
    $prospek = $this->prospekModel->getProspekById($id);
    echo json_encode($prospek);
  }

  public function convert($id, $internalCall = false)
  {
    if (!can('convert', 'leads')) {
      $message = 'Anda tidak memiliki hak akses untuk konversi.';
      if ($internalCall) return ['success' => false, 'message' => $message, 'data' => []];
      flash('prospek_message', $message, 'alert alert-danger');
      header('Location: ' . BASE_URL . '/prospek');
      exit;
    }

    $prospek = $this->prospekModel->getProspekById($id);
    if (!$prospek) {
      $message = 'Prospek tidak ditemukan.';
      if ($internalCall) return ['success' => false, 'message' => $message, 'data' => []];
      flash('prospek_message', $message, 'alert alert-danger');
      header('Location: ' . BASE_URL . '/prospek');
      exit;
    }

    $companyName = trim($prospek->company_name);
    $newCompanyId = 0;
    if (!empty($companyName)) {
      $existingCompany = $this->instansiModel->getInstansiByName($companyName);
      if ($existingCompany) $newCompanyId = $existingCompany->company_id;
    }

    if ($newCompanyId == 0) {
      $instansiData = [
        'name' => $companyName ?: 'Instansi ' . $prospek->name,
        'website' => '',
        'industry' => '',
        'description' => '',
        'gmaps_location' => ''
      ];
      $newCompanyId = $this->instansiModel->addInstansi($instansiData);
    }

    if (!$newCompanyId) {
      $message = 'Gagal membuat instansi saat konversi.';
      if ($internalCall) return ['success' => false, 'message' => $message, 'data' => []];
      flash('prospek_message', $message, 'alert alert-danger');
      header('Location: ' . BASE_URL . '/prospek');
      exit;
    }

    $kontakData = [
      'name' => $prospek->name,
      'email' => $prospek->email,
      'phone' => $prospek->phone,
      'company_id' => $newCompanyId
    ];

    if (!$this->kontakModel->addKontak($kontakData)) {
      $message = 'Gagal membuat kontak saat konversi.';
      if ($internalCall) return ['success' => false, 'message' => $message, 'data' => []];
      flash('prospek_message', $message, 'alert alert-danger');
      header('Location: ' . BASE_URL . '/prospek');
      exit;
    }

    $newContact = $this->kontakModel->getLastContactByCompanyId($newCompanyId);
    $newContactId = $newContact ? $newContact->contact_id : 0;

    $dealData = [
      'name' => 'Peluang dari ' . $prospek->name,
      'value' => 0,
      'owner_id' => $prospek->owner_id,
      'company_id' => $newCompanyId,
      'contact_id' => $newContactId,
      'stage' => 'Analisis Kebutuhan'
    ];

    $newDealId = $this->peluangModel->addPeluang($dealData);

    if ($newDealId) {
      $this->prospekModel->deleteProspek($id);
      $redirectUrl = BASE_URL . '/peluang/lengkapi/' . $newDealId;
      if ($internalCall) {
        return ['success' => true, 'message' => 'Prospek berhasil dikonversi!', 'data' => ['action' => 'redirect', 'url' => $redirectUrl]];
      }
      header('Location: ' . $redirectUrl);
      exit;
    } else {
      $message = 'Gagal membuat peluang saat konversi.';
      if ($internalCall) return ['success' => false, 'message' => $message, 'data' => []];
      flash('prospek_message', $message, 'alert alert-danger');
      header('Location: ' . BASE_URL . '/prospek');
      exit;
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

  public function addActivity($prospek_id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $data = [
        'name' => trim($_POST['activity_name']),
        'type' => $_POST['type'],
        'start_time' => $_POST['start_time'],
        'end_time' => !empty($_POST['end_time']) ? $_POST['end_time'] : null,
        'description' => trim($_POST['description']),
        'owner_id' => $_SESSION['user_id'],
        'related_item_id' => $prospek_id,
        // ==========================================================
        'related_item_type' => 'lead', // PERBAIKAN DI SINI
        // ==========================================================
        'documentation_photo' => null
      ];

      if (isset($_FILES['documentation_photo']) && $_FILES['documentation_photo']['error'] == 0) {
        $upload = $this->uploadFile($_FILES['documentation_photo']);
        if ($upload['success']) {
          $data['documentation_photo'] = $upload['filename'];
        } else {
          flash('prospek_message', $upload['message'], 'alert alert-danger');
          header('Location: ' . BASE_URL . '/prospek/detail/' . $prospek_id);
          exit;
        }
      }

      if ($this->activityModel->addActivity($data)) {
        flash('prospek_message', 'Aktivitas berhasil ditambahkan.');
        header('Location: ' . BASE_URL . '/prospek/detail/' . $prospek_id);
        exit;
      } else {
        die('Gagal menambahkan aktivitas.');
      }
    }
  }

  private function uploadFile($file)
  {
    $targetDir = "uploads/activities/";
    if (!file_exists($targetDir)) {
      mkdir($targetDir, 0777, true);
    }
    $fileName = uniqid() . '_' . basename($file["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
      return ['success' => true, 'filename' => $fileName];
    } else {
      return ['success' => false, 'message' => 'Gagal mengunggah file.'];
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
