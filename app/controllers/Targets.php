<?php
// app/controllers/Targets.php

class Targets extends Controller
{
  private $targetModel;
  private $userModel;

  public function __construct()
  {
    if (!isLoggedIn()) { /* ... */
    }
    if (!can('read', 'targets')) { /* ... */
    }
    $this->targetModel = $this->model('TargetModel');
    $this->userModel = $this->model('User');
  }

  public function index()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (!can('create', 'targets') && !can('update', 'targets')) {
        flash('target_message', 'Anda tidak memiliki izin untuk menyimpan target.', 'alert alert-danger');
        header('Location: ' . BASE_URL . '/targets');
        exit;
      }
      $month = $_POST['month'];
      $type = $_POST['type'];
      $targets = $_POST['targets'] ?? [];

      if ($this->targetModel->setOrUpdateTargets($targets, $month, $type)) {
        flash('target_message', 'Target pengguna berhasil disimpan.');
      } else {
        flash('target_message', 'Gagal menyimpan target.', 'error');
      }
      header('Location: ' . BASE_URL . '/targets?month=' . $month . '&type=' . $type);
      exit;
    } else {
      $selectedMonth = $_GET['month'] ?? date('Y-m');
      $selectedType = $_GET['type'] ?? 'penjualan'; // Default ke 'penjualan'

      // Definisikan jenis target yang tersedia di sini
      $targetTypes = [
        'penjualan' => ['name' => 'Penjualan', 'unit' => 'Rp'],
        'jumlah_deal' => ['name' => 'Jumlah Deal', 'unit' => 'Deal']
      ];

      $data = [
        'title' => 'Manajemen Target Pengguna',
        'targetable_users' => $this->userModel->getTargetableUsers(),
        'targets' => $this->targetModel->getTargets($selectedMonth, $selectedType),
        'target_types' => $targetTypes,
        'selected_month' => $selectedMonth,
        'selected_type' => $selectedType,
        'selected_unit' => $targetTypes[$selectedType]['unit']
      ];
      $this->renderView('pages/targets/index', $data);
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
