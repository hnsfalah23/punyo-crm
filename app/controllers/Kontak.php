<?php
// app/controllers/Kontak.php

class Kontak extends Controller
{
  private $instansiModel;

  public function __construct()
  {
    if (!isset($_SESSION['user_id'])) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    $this->instansiModel = $this->model('InstansiModel');
  }

  public function add($instansi_id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $data = [
        'name' => trim($_POST['name']),
        'contact_type' => trim($_POST['contact_type']),
        'priority' => $_POST['priority'],
        'email' => trim($_POST['email']),
        'job_title' => trim($_POST['job_title']),
        'phone' => trim($_POST['phone']),
        'company_id' => $instansi_id,
        'name_err' => ''
      ];

      if (empty($data['name'])) $data['name_err'] = 'Nama kontak tidak boleh kosong.';

      if (empty($data['name_err'])) {
        if ($this->instansiModel->addContact($data)) {
          flash('instansi_message', 'Kontak baru berhasil ditambahkan.');
          header('Location: ' . BASE_URL . '/instansi/detail/' . $instansi_id);
          exit;
        } else {
          die('Terjadi kesalahan.');
        }
      } else {
        $data['title'] = 'Tambah Kontak';
        $data['instansi'] = $this->instansiModel->getInstansiById($instansi_id);
        $this->renderView('pages/kontak/add', $data);
      }
    } else {
      $data = [
        'title' => 'Tambah Kontak',
        'instansi' => $this->instansiModel->getInstansiById($instansi_id),
        'name' => '',
        'contact_type' => '',
        'priority' => 'Sedang',
        'email' => '',
        'job_title' => '',
        'phone' => '',
        'name_err' => ''
      ];
      $this->renderView('pages/kontak/add', $data);
    }
  }

  public function edit($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $kontak = $this->instansiModel->getContactById($id);
      $data = [
        'id' => $id,
        'name' => trim($_POST['name']),
        'contact_type' => trim($_POST['contact_type']),
        'priority' => $_POST['priority'],
        'email' => trim($_POST['email']),
        'job_title' => trim($_POST['job_title']),
        'phone' => trim($_POST['phone']),
        'company_id' => $kontak->company_id,
        'name_err' => ''
      ];

      if (empty($data['name'])) $data['name_err'] = 'Nama kontak tidak boleh kosong.';

      if (empty($data['name_err'])) {
        if ($this->instansiModel->updateContact($data)) {
          flash('instansi_message', 'Data kontak berhasil diupdate.');
          header('Location: ' . BASE_URL . '/instansi/detail/' . $data['company_id']);
          exit;
        } else {
          die('Terjadi kesalahan.');
        }
      } else {
        $data['title'] = 'Edit Kontak';
        $data['instansi'] = $this->instansiModel->getInstansiById($data['company_id']);
        $this->renderView('pages/kontak/edit', $data);
      }
    } else {
      $kontak = $this->instansiModel->getContactById($id);
      if (!$kontak) {
        header('Location: ' . BASE_URL . '/instansi');
        exit;
      }
      $data = [
        'title' => 'Edit Kontak',
        'instansi' => $this->instansiModel->getInstansiById($kontak->company_id),
        'id' => $id,
        'name' => $kontak->name,
        'contact_type' => $kontak->contact_type,
        'priority' => $kontak->priority,
        'email' => $kontak->email,
        'job_title' => $kontak->job_title,
        'phone' => $kontak->phone,
        'name_err' => ''
      ];
      $this->renderView('pages/kontak/edit', $data);
    }
  }

  public function delete($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $kontak = $this->instansiModel->getContactById($id);
      if ($this->instansiModel->deleteContact($id)) {
        flash('instansi_message', 'Kontak berhasil dihapus.');
        header('Location: ' . BASE_URL . '/instansi/detail/' . $kontak->company_id);
        exit;
      }
    } else {
      header('Location: ' . BASE_URL . '/instansi');
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
