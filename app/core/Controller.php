<?php
// app/core/Controller.php

/**
 * Controller dasar
 * Memuat model dan view
 */
class Controller
{
  /**
   * Memuat file model dan menginstansiasinya.
   *
   * @param string $model Nama kelas model.
   * @return object Instance dari model.
   */
  public function model($model)
  {
    // Path yang benar untuk memuat model dari direktori app/models/
    $modelPath = '../app/models/' . $model . '.php';

    // Cek apakah file model ada sebelum memuatnya
    if (file_exists($modelPath)) {
      require_once $modelPath;
      // Mengembalikan instance baru dari kelas model
      return new $model();
    } else {
      // Hentikan aplikasi jika model tidak ditemukan
      die('Model tidak ditemukan: ' . $modelPath);
    }
  }

  /**
   * Memuat file view.
   *
   * @param string $view Nama file view.
   * @param array $data Data untuk diekstrak menjadi variabel di dalam view.
   */
  public function view($view, $data = [])
  {
    // Path ke file view
    $viewPath = '../app/views/' . $view . '.php';

    if (file_exists($viewPath)) {
      // Ekstrak data menjadi variabel individual
      extract($data);

      require_once $viewPath;
    } else {
      // Hentikan aplikasi jika view tidak ditemukan
      die('View tidak ditemukan: ' . $viewPath);
    }
  }
}
