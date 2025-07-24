<?php
// app/core/Controller.php

class Controller
{
  // Method untuk memuat view
  public function view($view, $data = [])
  {
    // Ekstrak data agar bisa diakses sebagai variabel di view
    extract($data);

    if (file_exists('../app/views/' . $view . '.php')) {
      require_once '../app/views/' . $view . '.php';
    } else {
      die('View tidak ditemukan: ' . $view);
    }
  }

  // Method untuk memuat model
  public function model($model)
  {
    require_once '../app/models/' . $model . '.php';
    return new $model();
  }
}
