<?php
// app/controllers/Kontak.php

class Kontak extends Controller
{
  private $kontakModel;

  public function __construct()
  {
    if (!isLoggedIn()) {
      if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $this->jsonResponse(false, 'Sesi Anda telah berakhir. Silakan login kembali.', 401);
      } else {
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
      }
    }
    $this->kontakModel = $this->model('KontakModel');
  }

  /**
   * Menangani penambahan kontak baru dari form via AJAX.
   */
  public function add()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !can('create', 'kontak')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $data = [
      'name' => trim($_POST['name'] ?? ''),
      'email' => trim($_POST['email'] ?? ''),
      'phone' => trim($_POST['phone'] ?? ''),
      'company_id' => trim($_POST['company_id'] ?? '')
    ];

    if (empty($data['name']) || empty($data['company_id'])) {
      $this->jsonResponse(false, 'Nama kontak dan instansi harus diisi.');
      return;
    }

    try {
      if ($this->kontakModel->addKontak($data)) {
        $this->jsonResponse(true, 'Kontak baru berhasil ditambahkan.');
      } else {
        $this->jsonResponse(false, 'Gagal menambahkan kontak ke database.');
      }
    } catch (PDOException $e) {
      // Tangani error database dan kirim respons JSON yang benar
      // Anda bisa mencatat error ini ke file log jika perlu: error_log($e->getMessage());
      $this->jsonResponse(false, 'Terjadi kesalahan pada server.', 500);
    }
  }

  /**
   * Mengambil data satu kontak untuk form edit via AJAX.
   */
  public function getKontakData($id)
  {
    if (!can('read', 'kontak')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }
    $kontak = $this->kontakModel->getKontakById((int)$id);
    if ($kontak) {
      $this->jsonResponse(true, 'Data ditemukan', 200, (array)$kontak);
    } else {
      $this->jsonResponse(false, 'Data kontak tidak ditemukan.', 404);
    }
  }

  /**
   * Memperbarui data kontak via AJAX.
   */
  public function edit()
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !can('update', 'kontak')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    $data = [
      'contact_id' => (int)($_POST['contact_id'] ?? 0),
      'name' => trim($_POST['name'] ?? ''),
      'email' => trim($_POST['email'] ?? ''),
      'phone' => trim($_POST['phone'] ?? '')
    ];

    if ($data['contact_id'] === 0 || empty($data['name'])) {
      $this->jsonResponse(false, 'ID Kontak dan Nama tidak boleh kosong.');
      return;
    }

    try {
      if ($this->kontakModel->updateKontak($data)) {
        $this->jsonResponse(true, 'Data kontak berhasil diperbarui.');
      } else {
        $this->jsonResponse(false, 'Gagal memperbarui data kontak.');
      }
    } catch (PDOException $e) {
      $this->jsonResponse(false, 'Terjadi kesalahan pada server.', 500);
    }
  }

  /**
   * Menghapus kontak via AJAX.
   */
  public function delete($id)
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !can('delete', 'kontak')) {
      $this->jsonResponse(false, 'Akses tidak diizinkan.', 403);
    }

    $id = (int)$id;
    if ($id === 0) {
      $this->jsonResponse(false, 'ID kontak tidak valid.');
      return;
    }

    try {
      if ($this->kontakModel->deleteKontak($id)) {
        $this->jsonResponse(true, 'Kontak berhasil dihapus.');
      } else {
        $this->jsonResponse(false, 'Gagal menghapus kontak.');
      }
    } catch (PDOException $e) {
      $this->jsonResponse(false, 'Terjadi kesalahan pada server.', 500);
    }
  }

  private function jsonResponse($success, $message, $httpCode = 200, $data = [])
  {
    // PERBAIKAN: Bersihkan output buffer untuk mencegah error/warning merusak JSON
    if (ob_get_level() > 0) {
      ob_end_clean();
    }

    header('Content-Type: application/json');
    http_response_code($httpCode);
    $response = ['success' => $success, 'message' => $message];
    if (!empty($data)) {
      $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
  }
}
