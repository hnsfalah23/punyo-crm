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

    // Dengan sistem Divisi, Instansi adalah data bersama.
    // Siapa pun yang bisa mengakses menu ini bisa melihat semua Instansi.
    $data = [
      'title' => 'Manajemen Instansi',
      'instansi' => $this->instansiModel->getAllInstansi()
    ];
    $this->renderView('pages/instansi/index', $data);
  }

  public function add()
  {
    if (!can('create', 'instansi')) {
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $data = [
        'name' => trim($_POST['name']),
        'website' => trim($_POST['website']),
        'industry' => trim($_POST['industry']),
        'description' => trim($_POST['description']),
        'gmaps_location' => trim($_POST['gmaps_location']),
        'name_err' => ''
      ];
      if (empty($data['name'])) $data['name_err'] = 'Nama instansi tidak boleh kosong.';
      if (empty($data['name_err'])) {
        if ($this->instansiModel->addInstansi($data)) {
          flash('instansi_message', 'Instansi baru berhasil ditambahkan.');
          header('Location: ' . BASE_URL . '/instansi');
          exit;
        }
      } else {
        $data['title'] = 'Tambah Instansi';
        $this->renderView('pages/instansi/add', $data);
      }
    } else {
      $data = [
        'title' => 'Tambah Instansi',
        'name' => '',
        'website' => '',
        'industry' => '',
        'description' => '',
        'gmaps_location' => '',
        'name_err' => ''
      ];
      $this->renderView('pages/instansi/add', $data);
    }
  }

  public function edit($id)
  {
    if (!can('update', 'instansi')) {
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $data = [
        'id' => $id,
        'name' => trim($_POST['name']),
        'website' => trim($_POST['website']),
        'industry' => trim($_POST['industry']),
        'description' => trim($_POST['description']),
        'gmaps_location' => trim($_POST['gmaps_location']),
        'name_err' => ''
      ];
      if (empty($data['name'])) $data['name_err'] = 'Nama instansi tidak boleh kosong.';
      if (empty($data['name_err'])) {
        if ($this->instansiModel->updateInstansi($data)) {
          flash('instansi_message', 'Data instansi berhasil diupdate.');
          header('Location: ' . BASE_URL . '/instansi');
          exit;
        }
      } else {
        $data['title'] = 'Edit Instansi';
        $this->renderView('pages/instansi/edit', $data);
      }
    } else {
      $instansi = $this->instansiModel->getInstansiById($id);
      if (!$instansi) {
        header('Location: ' . BASE_URL . '/instansi');
        exit;
      }
      $data = [
        'title' => 'Edit Instansi',
        'id' => $id,
        'name' => $instansi->name,
        'website' => $instansi->website,
        'industry' => $instansi->industry,
        'description' => $instansi->description,
        'gmaps_location' => $instansi->gmaps_location,
        'name_err' => ''
      ];
      $this->renderView('pages/instansi/edit', $data);
    }
  }

  public function delete($id)
  {
    if (!can('delete', 'instansi')) {
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($this->instansiModel->deleteInstansi($id)) {
        flash('instansi_message', 'Instansi berhasil dihapus.');
      } else {
        flash('instansi_message', 'Gagal menghapus instansi.', 'error');
      }
      header('Location: ' . BASE_URL . '/instansi');
      exit;
    }
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

    // Ambil data terkait
    $kontak = $this->instansiModel->getContactsByInstansiId($id);
    $deals = $this->instansiModel->getDealsByInstansiId($id); // <-- Perbaikan: Ambil data kesepakatan

    $data = [
      'title' => 'Detail Instansi',
      'instansi' => $instansi,
      'kontak' => $kontak,
      'deals' => $deals // <-- Perbaikan: Kirim data kesepakatan ke view
    ];
    $this->renderView('pages/instansi/detail', $data);
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
