<?php
// app/core/Router.php

class Router
{
  protected $currentController = 'Home';
  protected $currentMethod = 'index';
  protected $params = [];

  public function __construct()
  {
    $url = $this->getUrl();

    // Cek apakah controller ada
    if (isset($url[0]) && file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
      $this->currentController = ucwords($url[0]);
      unset($url[0]);
    }

    // Panggil controller
    require_once '../app/controllers/' . $this->currentController . '.php';
    $this->currentController = new $this->currentController;

    // Cek apakah method ada
    if (isset($url[1])) {
      if (method_exists($this->currentController, $url[1])) {
        $this->currentMethod = $url[1];
        unset($url[1]);
      }
    }

    // Ambil parameter
    $this->params = $url ? array_values($url) : [];

    // Jalankan controller & method, serta kirimkan params jika ada
    call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
  }

  public function getUrl()
  {
    if (isset($_GET['url'])) {
      $url = rtrim($_GET['url'], '/');
      $url = filter_var($url, FILTER_SANITIZE_URL);
      $url = explode('/', $url);
      return $url;
    }
    return [];
  }
}
