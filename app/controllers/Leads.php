<?php
// app/controllers/Leads.php

class Leads extends Controller
{
  private $leadModel;
  private $activityModel;
  private $instansiModel;
  private $dealModel;

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
  }

  public function index()
  {
    if (!can('read', 'leads')) {
      flash('dashboard_message', 'Anda tidak memiliki hak akses.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }

    $scope_type = 'all';
    $scope_value = null;
    $user_role_id = $_SESSION['user_role_id'];
    $user_id = $_SESSION['user_id'];

    $userModel = $this->model('User');
    $currentUser = $userModel->getUserById($user_id);
    $user_division_id = $currentUser->division_id ?? null;

    if (in_array($user_role_id, [3, 4, 5])) { // Manajer, SPV
      $scope_type = 'division';
      $scope_value = $user_division_id;
    } elseif ($user_role_id == 6) { // Staf
      $scope_type = 'self';
      $scope_value = $user_id;
    }

    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $limit = $_GET['limit'] ?? 10;
    $page = $_GET['page'] ?? 1;

    $params = [
      'search' => $search,
      'status' => $status,
      'limit' => $limit,
      'offset' => ($page - 1) * $limit,
      'scope_type' => $scope_type,
      'scope_value' => $scope_value
    ];

    $leads = $this->leadModel->getLeads($params);
    $totalLeads = $this->leadModel->getTotalLeads($params);
    $totalPages = ($limit > 0) ? ceil($totalLeads / $limit) : 1;

    $data = [
      'title' => 'Manajemen Prospek',
      'leads' => $leads,
      'total_leads' => $totalLeads,
      'total_pages' => $totalPages,
      'current_page' => $page,
      'limit' => $limit,
      'search' => $search,
      'status' => $status
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
    if (!can('create', 'leads')) {
      header('Location: ' . BASE_URL . '/leads');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $data = [
        'name' => trim($_POST['name']),
        'status' => $_POST['status'],
        'source' => trim($_POST['source']),
        'company_name' => trim($_POST['company_name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']),
        'owner_id' => $_SESSION['user_id'],
        'name_err' => ''
      ];

      if (empty($data['name'])) $data['name_err'] = 'Nama prospek tidak boleh kosong.';

      if (empty($data['name_err'])) {
        if ($this->leadModel->addLead($data)) {
          flash('lead_message', 'Prospek baru berhasil ditambahkan.');
          header('Location: ' . BASE_URL . '/leads');
          exit;
        }
      } else {
        $data['title'] = 'Tambah Prospek';
        $this->renderView('pages/leads/add', $data);
      }
    } else {
      $data = [
        'title' => 'Tambah Prospek',
        'name' => '',
        'status' => 'Baru',
        'source' => '',
        'company_name' => '',
        'email' => '',
        'phone' => '',
        'name_err' => ''
      ];
      $this->renderView('pages/leads/add', $data);
    }
  }

  public function edit($id)
  {
    if (!can('update', 'leads')) {
      header('Location: ' . BASE_URL . '/leads');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $data = [
        'id' => $id,
        'name' => trim($_POST['name']),
        'status' => $_POST['status'],
        'source' => trim($_POST['source']),
        'company_name' => trim($_POST['company_name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']),
        'name_err' => ''
      ];

      if (empty($data['name'])) $data['name_err'] = 'Nama prospek tidak boleh kosong.';

      if (empty($data['name_err'])) {
        if ($this->leadModel->updateLead($data)) {
          flash('lead_message', 'Data prospek berhasil diupdate.');
          header('Location: ' . BASE_URL . '/leads');
          exit;
        }
      } else {
        $data['title'] = 'Edit Prospek';
        $this->renderView('pages/leads/edit', $data);
      }
    } else {
      $lead = $this->leadModel->getLeadById($id);
      if (!$lead) {
        header('Location: ' . BASE_URL . '/leads');
        exit;
      }
      $data = [
        'title' => 'Edit Prospek',
        'id' => $id,
        'name' => $lead->name,
        'status' => $lead->status,
        'source' => $lead->source,
        'company_name' => $lead->company_name,
        'email' => $lead->email,
        'phone' => $lead->phone,
        'name_err' => ''
      ];
      $this->renderView('pages/leads/edit', $data);
    }
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

  public function convert($id)
  {
    if (!can('create', 'deals')) {
      header('Location: ' . BASE_URL . '/leads');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $companyName = trim($_POST['company_name'] ?? '');
      $existingCompany = $this->instansiModel->getInstansiByName($companyName);

      if ($existingCompany) {
        $newCompanyId = $existingCompany->company_id;
      } else {
        $instansiData = [
          'name' => $companyName,
          'website' => '',
          'industry' => '',
          'description' => 'Dikonversi dari prospek: ' . trim($_POST['contact_name'] ?? ''),
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
            'name' => trim($_POST['contact_name'] ?? ''), // Perbaikan: Langsung menggunakan nama kontak
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
