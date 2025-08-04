<?php
// app/controllers/Instansi.php

class Instansi extends Controller
{
  private $instansiModel;

  public function __construct()
  {
    if (!isLoggedIn()) {
      if ($this->isAjaxRequest()) {
        $this->jsonResponse(false, 'Sesi Anda telah berakhir. Silakan login kembali.', 401);
      } else {
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
      }
    }
    $this->instansiModel = $this->model('InstansiModel');
  }

  /**
   * Menampilkan halaman utama manajemen instansi.
   */
  public function index()
  {
    if (!can('read', 'instansi')) {
      flash('dashboard_message', 'Anda tidak memiliki hak akses untuk melihat halaman ini.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }

    $search = $_GET['search'] ?? '';
    $filter_industry = $_GET['filter_industry'] ?? '';
    $limit = 10;
    $page = (int) ($_GET['page'] ?? 1);
    $offset = ($page - 1) * $limit;

    $params = compact('search', 'filter_industry', 'limit', 'offset');

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
      'all_instansi' => $this->instansiModel->getInstansi()
    ];

    $this->renderView('pages/instansi/index', $data);
  }

  /**
   * Menampilkan halaman detail instansi.
   */
  public function detail($id)
  {
    if (!can('read', 'instansi')) {
      flash('instansi_message', 'Anda tidak memiliki hak akses.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }

    $instansi = $this->instansiModel->getInstansiById($id);
    if (!$instansi) {
      flash('instansi_message', 'Instansi tidak ditemukan.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }

    $data = [
      'title' => 'Detail Instansi: ' . $instansi->name,
      'instansi' => $instansi,
      'kontak' => $this->instansiModel->getContactsByInstansiId($id),
      'deals' => $this->instansiModel->getDealsByInstansiId($id),
      'products' => $this->instansiModel->getProductsByInstansiId($id)
    ];

    $this->renderView('pages/instansi/detail', $data);
  }

  /**
   * Menambahkan instansi baru. Ditangani via AJAX dan mengembalikan JSON.
   */
  public function add()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !can('create', 'instansi')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    $data = $this->sanitizeInput($_POST);

    if (empty($data['name'])) {
      $this->jsonResponse(false, 'Nama instansi tidak boleh kosong.');
    }

    try {
      if ($this->instansiModel->addInstansi($data)) {
        $this->jsonResponse(true, 'Instansi baru berhasil ditambahkan.');
      } else {
        $this->jsonResponse(false, 'Gagal menambahkan instansi ke database.');
      }
    } catch (PDOException $e) {
      $this->jsonResponse(false, 'Terjadi kesalahan pada server.', 500);
    }
  }

  /**
   * Memperbarui data instansi. Ditangani via AJAX.
   */
  public function edit()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !can('update', 'instansi')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    $data = $this->sanitizeInput($_POST);
    $id = (int) ($data['company_id'] ?? 0);

    if ($id === 0) {
      $this->jsonResponse(false, 'ID instansi tidak valid.');
    }
    if (empty($data['name'])) {
      $this->jsonResponse(false, 'Nama instansi tidak boleh kosong.');
    }

    $data['id'] = $id;

    try {
      if ($this->instansiModel->updateInstansi($data)) {
        $this->jsonResponse(true, 'Data instansi berhasil diperbarui.');
      } else {
        $this->jsonResponse(false, 'Gagal memperbarui data instansi.');
      }
    } catch (PDOException $e) {
      $this->jsonResponse(false, 'Terjadi kesalahan pada server.', 500);
    }
  }

  /**
   * Menghapus instansi. Ditangani via AJAX.
   */
  public function delete($id)
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !can('delete', 'instansi')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    $id = (int) $id;
    if ($id === 0) {
      $this->jsonResponse(false, 'ID instansi tidak valid.');
    }

    try {
      if ($this->instansiModel->hasRelatedData($id)) {
        $this->jsonResponse(false, 'Gagal menghapus. Instansi masih memiliki kontak atau peluang terkait.');
        return;
      }

      if ($this->instansiModel->deleteInstansi($id)) {
        $this->jsonResponse(true, 'Instansi berhasil dihapus.');
      } else {
        $this->jsonResponse(false, 'Gagal menghapus instansi dari database.');
      }
    } catch (PDOException $e) {
      $this->jsonResponse(false, 'Terjadi kesalahan pada server.', 500);
    }
  }

  /**
   * Mengambil data satu instansi untuk form edit via AJAX.
   */
  public function getInstansiData($id)
  {
    if (!can('read', 'instansi')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    $instansi = $this->instansiModel->getInstansiById((int) $id);

    if ($instansi) {
      $this->jsonResponse(true, 'Data ditemukan', 200, (array) $instansi);
    } else {
      $this->jsonResponse(false, 'Data instansi tidak ditemukan.', 404);
    }
  }

  /**
   * Mengambil data terkait (kontak/peluang) untuk modal.
   */
  public function getRelatedData($id, $type)
  {
    if (!can('read', 'instansi')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    $id = (int)$id;
    $data = [];

    if ($type === 'kontak') {
      $data = $this->instansiModel->getContactsByInstansiId($id);
    } elseif ($type === 'peluang') {
      $data = $this->instansiModel->getDealsByInstansiId($id);
    } else {
      $this->jsonResponse(false, 'Tipe data tidak valid.', 400);
    }

    $this->jsonResponse(true, 'Data berhasil diambil', 200, $data);
  }

  private function isAjaxRequest()
  {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
  }

  private function sanitizeInput($postData)
  {
    return [
      'company_id' => $postData['company_id'] ?? null,
      'name' => trim($postData['name'] ?? ''),
      'website' => trim($postData['website'] ?? ''),
      'industry' => trim($postData['industry'] ?? ''),
      'description' => trim($postData['description'] ?? ''),
      'gmaps_location' => trim($postData['gmaps_location'] ?? '')
    ];
  }

  private function jsonResponse($success, $message, $httpCode = 200, $data = [])
  {
    header('Content-Type: application/json');
    http_response_code($httpCode);
    $response = ['success' => $success, 'message' => $message];
    if (!empty($data)) {
      $response['data'] = $data;
    }
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
