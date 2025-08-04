<?php
// app/controllers/Leads.php

class Leads extends Controller
{
  private $leadModel;
  private $activityModel;
  private $instansiModel;
  private $dealModel;
  private $userModel;

  public function __construct()
  {
    if (!isLoggedIn()) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    $this->leadModel = $this->model('LeadModel');
    $this->activityModel = $this->model('ActivityModel');
    $this->instansiModel = $this->model('InstansiModel');
    $this->dealModel = $this->model('DealModel');
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

    $leads = $this->leadModel->getLeads($params);
    $totalLeads = $this->leadModel->getTotalLeads($params);
    $totalPages = ($params['limit'] > 0) ? ceil($totalLeads / $params['limit']) : 1;

    // Mengambil semua prospek (tanpa paginasi) untuk modal
    $allLeadsForActivity = $this->leadModel->getLeads(['scope_type' => $scope['type'], 'scope_value' => $scope['value']]);

    $data = [
      'title' => 'Manajemen Prospek',
      'leads' => $leads,
      'total_leads' => $totalLeads,
      'total_pages' => $totalPages,
      'current_page' => $_GET['page'] ?? 1,
      'limit' => $params['limit'],
      'search' => $params['search'],
      'status' => $params['status'],
      'all_leads_for_activity' => $allLeadsForActivity
    ];
    $this->renderView('pages/leads/index', $data);
  }


  public function detail($id)
  {
    if (!can('read', 'leads')) {
      header('Location: ' . BASE_URL . '/leads');
      exit;
    }
    $lead = $this->leadModel->getLeadById($id);
    if (!$lead) {
      header('Location: ' . BASE_URL . '/leads');
      exit;
    }
    $data = [
      'title' => 'Detail Prospek',
      'lead' => $lead,
      'activities' => $this->activityModel->getActivitiesByItemId($id, 'lead')
    ];
    $this->renderView('pages/leads/detail', $data);
  }

  public function add()
  {
    if (!can('create', 'leads') || $_SERVER['REQUEST_METHOD'] != 'POST') {
      header('Location: ' . BASE_URL . '/leads');
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
      if ($this->leadModel->addLead($data)) {
        flash('lead_message', 'Prospek baru berhasil ditambahkan.');
      } else {
        flash('lead_message', 'Gagal menambahkan prospek.', 'alert alert-danger');
      }
    }
    header('Location: ' . BASE_URL . '/leads');
    exit;
  }

  public function edit($id)
  {
    if (!can('update', 'leads') || $_SERVER['REQUEST_METHOD'] != 'POST') {
      header('Location: ' . BASE_URL . '/leads');
      exit;
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

    // **PERBAIKAN UTAMA DI SINI:** Logika Redirect
    $redirect_url = $_POST['redirect_url'] ?? BASE_URL . '/leads/detail/' . $id;

    if (empty($data['name'])) {
      flash('lead_message', 'Nama prospek tidak boleh kosong.', 'alert alert-danger');
    } else {
      $originalLead = $this->leadModel->getLeadById($id);
      if ($this->leadModel->updateLead($data)) {
        flash('lead_message', 'Data prospek berhasil diupdate.');

        if ($data['status'] == 'Kualifikasi' && $originalLead->status != 'Kualifikasi') {
          $this->quickConvert($id);
          return;
        }
      } else {
        flash('lead_message', 'Gagal mengupdate data prospek.', 'alert alert-danger');
      }
    }
    header('Location: ' . $redirect_url);
    exit;
  }
  
  public function getLeadJson($id)
  {
    header('Content-Type: application/json');
    $lead = $this->leadModel->getLeadById($id);
    echo json_encode($lead);
  }


  public function delete($id)
  {
    if (!can('delete', 'leads')) {
      header('Location: ' . BASE_URL . '/leads');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($this->leadModel->deleteLead($id)) {
        flash('lead_message', 'Prospek berhasil dihapus.');
        header('Location: ' . BASE_URL . '/leads');
        exit;
      }
    } else {
      header('Location: ' . BASE_URL . '/leads');
      exit;
    }
  }

  public function quickConvert($id)
  {
    if (!can('create', 'deals')) {
      header('Location: ' . BASE_URL . '/leads');
      exit;
    }
    $lead = $this->leadModel->getLeadById($id);
    if (!$lead) {
      flash('lead_message', 'Prospek tidak ditemukan.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/leads');
      exit;
    }

    $companyName = trim($lead->company_name);
    $existingCompany = $this->instansiModel->getInstansiByName($companyName);

    if ($existingCompany) {
      $newCompanyId = $existingCompany->company_id;
    } else {
      // **PERBAIKAN UTAMA DI SINI:** Kosongkan jika tidak ada
      $instansiData = ['name' => $companyName, 'description' => ''];
      $newCompanyId = $this->instansiModel->addInstansi($instansiData);
    }

    if ($newCompanyId) {
      $kontakData = [
        'name' => $lead->name,
        'email' => $lead->email,
        'phone' => $lead->phone,
        'job_title' => 'Kontak Utama',
        'company_id' => $newCompanyId,
        'contact_type' => 'Prospek',
        'priority' => 'Sedang'
      ];
      $newContactId = $this->instansiModel->addContact($kontakData);

      if ($newContactId) {
        $dealData = [
          'name' => 'Peluang dari ' . $lead->name,
          'value' => 0,
          'owner_id' => $lead->owner_id,
          'company_id' => $newCompanyId,
          'contact_id' => $newContactId,
          'stage' => 'Analisis Kebutuhan',
          'expected_close_date' => null
        ];
        $this->dealModel->addDeal($dealData);
        $this->leadModel->deleteLead($id);
        flash('instansi_message', 'Prospek berhasil dikonversi. Silakan lengkapi data instansi.');
        header('Location: ' . BASE_URL . '/instansi/edit/' . $newCompanyId);
        exit;
      }
    }
    flash('lead_message', 'Gagal melakukan konversi.', 'alert alert-danger');
    header('Location: ' . BASE_URL . '/leads');
    exit;
  }


  public function convert($id)
  {
    if (!can('create', 'deals')) {
      header('Location: ' . BASE_URL . '/leads');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['quick_convert'])) {
      $companyName = trim($_POST['company_name'] ?? '');
      $existingCompany = $this->instansiModel->getInstansiByName($companyName);
      if ($existingCompany) {
        $newCompanyId = $existingCompany->company_id;
      } else {
        // **PERBAIKAN UTAMA DI SINI:** Kosongkan jika tidak ada
        $instansiData = [
          'name' => $companyName,
          'website' => '',
          'industry' => '',
          'description' => '',
          'gmaps_location' => ''
        ];
        $newCompanyId = $this->instansiModel->addInstansi($instansiData);
      }
      if ($newCompanyId) {
        $kontakData = [
          'name' => trim($_POST['contact_name'] ?? ''),
          'email' => trim($_POST['email'] ?? ''),
          'phone' => trim($_POST['phone'] ?? ''),
          'job_title' => 'Kontak Utama',
          'company_id' => $newCompanyId,
          'contact_type' => 'Prospek',
          'priority' => 'Sedang'
        ];
        $newContactId = $this->instansiModel->addContact($kontakData);
        if ($newContactId) {
          $dealData = [
            'name' => trim($_POST['deal_name'] ?? ''),
            'value' => trim($_POST['deal_value'] ?? 0),
            'owner_id' => $_SESSION['user_id'],
            'company_id' => $newCompanyId,
            'contact_id' => $newContactId,
            'stage' => 'Analisis Kebutuhan',
            'expected_close_date' => null
          ];
          $this->dealModel->addDeal($dealData);
          $this->leadModel->deleteLead($id);
          flash('instansi_message', 'Prospek berhasil dikonversi.');
          header('Location: ' . BASE_URL . '/instansi/detail/' . $newCompanyId);
          exit;
        }
      }
      die('Gagal membuat instansi atau kontak baru.');
    } else {
      $lead = $this->leadModel->getLeadById($id);
      if (!$lead || $lead->status != 'Kualifikasi') {
        header('Location: ' . BASE_URL . '/leads');
        exit;
      }
      $data = ['title' => 'Konversi Prospek', 'lead' => $lead];
      $this->renderView('pages/leads/convert', $data);
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
