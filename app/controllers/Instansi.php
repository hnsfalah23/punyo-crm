<?php
// app/controllers/Instansi.php

class Instansi extends Controller
{
  private $instansiModel;

  public function __construct()
  {
    if (!isLoggedIn()) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    $this->instansiModel = $this->model('InstansiModel');
  }

  public function index()
  {
    if (!can('read', 'instansi')) {
      flash('dashboard_message', 'Anda tidak memiliki hak akses.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }

    $search = $_GET['search'] ?? '';
    $filter_industry = $_GET['filter_industry'] ?? '';
    $limit = 10;
    $page = $_GET['page'] ?? 1;
    $offset = ($page - 1) * $limit;

    $params = [
      'search' => $search,
      'filter_industry' => $filter_industry,
      'limit' => $limit,
      'offset' => $offset
    ];

    // **PERBAIKAN UTAMA DI SINI:** Menggunakan nama fungsi yang benar
    $instansi = $this->instansiModel->getInstansi($params);
    $totalInstansi = $this->instansiModel->getTotalInstansi($params);
    $totalPages = ceil($totalInstansi / $limit);

    $data = [
      'title' => 'Manajemen Instansi',
      'instansi' => $instansi,
      'total_instansi' => $totalInstansi,
      'total_pages' => $totalPages,
      'current_page' => $page,
      'search' => $search,
      'filter_industry' => $filter_industry,
      'industries' => $this->instansiModel->getDistinctIndustries(),
      'all_instansi' => $this->instansiModel->getInstansi() // Untuk dropdown di modal
    ];

    $this->renderView('pages/instansi/index', $data);
  }

  public function detail($id)
  {
    if (!can('read', 'instansi')) {
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }
    $instansi = $this->instansiModel->getInstansiById($id);
    if (!$instansi) {
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }

    $data = [
      'title' => 'Detail Instansi',
      'instansi' => $instansi,
      'kontak' => $this->instansiModel->getContactsByInstansiId($id),
      'deals' => $this->instansiModel->getDealsByInstansiId($id)
    ];
    $this->renderView('pages/instansi/detail', $data);
  }

  public function add()
  {
    if (!can('create', 'instansi') || $_SERVER['REQUEST_METHOD'] != 'POST') {
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }

    $data = [
      'name' => trim($_POST['name']),
      'website' => trim($_POST['website']),
      'industry' => trim($_POST['industry']),
      'description' => trim($_POST['description']),
      'gmaps_location' => trim($_POST['gmaps_location'])
    ];

    if (empty($data['name'])) {
      flash('instansi_message', 'Nama instansi tidak boleh kosong.', 'alert alert-danger');
    } else {
      if ($this->instansiModel->addInstansi($data)) {
        flash('instansi_message', 'Instansi baru berhasil ditambahkan.');
      } else {
        flash('instansi_message', 'Gagal menambahkan instansi.', 'alert alert-danger');
      }
    }
    header('Location: ' . BASE_URL . '/instansi');
    exit;
  }

  public function edit($id = null)
  {
    if (!can('update', 'instansi')) {
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Jika ada company_id dari modal form, gunakan itu
      $company_id = $_POST['company_id'] ?? $id;

      if (!$company_id) {
        flash('instansi_message', 'ID instansi tidak valid.', 'alert alert-danger');
        header('Location: ' . BASE_URL . '/instansi');
        exit;
      }

      $data = [
        'id' => $company_id,
        'name' => trim($_POST['name']),
        'website' => trim($_POST['website']),
        'industry' => trim($_POST['industry']),
        'description' => trim($_POST['description']),
        'gmaps_location' => trim($_POST['gmaps_location'])
      ];

      if (empty($data['name'])) {
        flash('instansi_message', 'Nama instansi tidak boleh kosong.', 'alert alert-danger');
      } else {
        if ($this->instansiModel->updateInstansi($data)) {
          flash('instansi_message', 'Data instansi berhasil diupdate.');
        } else {
          flash('instansi_message', 'Gagal mengupdate instansi.', 'alert alert-danger');
        }
      }
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }

    // Jika bukan POST request dan ada ID, redirect ke halaman utama
    // karena sekarang menggunakan modal
    if ($id) {
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }
  }

  public function delete($id)
  {
    if (!can('delete', 'instansi')) {
      flash('instansi_message', 'Anda tidak memiliki izin untuk menghapus.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Cek apakah ada data terkait
      $relatedContacts = $this->instansiModel->getContactsByInstansiId($id);
      $relatedDeals = $this->instansiModel->getDealsByInstansiId($id);

      if (!empty($relatedContacts) || !empty($relatedDeals)) {
        $errorMessage = 'Gagal menghapus instansi. Masih ada data yang terkait:';
        if (!empty($relatedContacts)) {
          $errorMessage .= ' ' . count($relatedContacts) . ' kontak';
        }
        if (!empty($relatedDeals)) {
          $errorMessage .= (!empty($relatedContacts) ? ' dan ' : ' ') . count($relatedDeals) . ' peluang.';
        }

        flash('instansi_message', $errorMessage, 'alert alert-danger');
        header('Location: ' . BASE_URL . '/instansi');
        exit;
      }

      if ($this->instansiModel->deleteInstansi($id)) {
        flash('instansi_message', 'Instansi berhasil dihapus.');
      } else {
        flash('instansi_message', 'Gagal menghapus instansi dari database.', 'alert alert-danger');
      }
    }

    header('Location: ' . BASE_URL . '/instansi');
    exit;
  }

  // Method untuk mendapatkan data instansi via AJAX (opsional)
  public function getInstansiData($id)
  {
    if (!can('read', 'instansi')) {
      http_response_code(403);
      echo json_encode(['error' => 'Unauthorized']);
      exit;
    }

    header('Content-Type: application/json');

    $instansi = $this->instansiModel->getInstansiById($id);

    if ($instansi) {
      echo json_encode([
        'success' => true,
        'data' => [
          'company_id' => $instansi->company_id,
          'name' => $instansi->name,
          'industry' => $instansi->industry,
          'website' => $instansi->website,
          'description' => $instansi->description,
          'gmaps_location' => $instansi->gmaps_location
        ]
      ]);
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'Data instansi tidak ditemukan'
      ]);
    }
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
