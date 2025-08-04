<?php
// app/core/Router.php

class Router
{
  protected $currentController = 'Dashboard'; // Controller default
  protected $currentMethod = 'index';
  protected $params = [];

  public function __construct()
  {
    $url = $this->getUrl();

    // Cek controller dari URL
    if (isset($url[0])) {
      // Ubah huruf pertama menjadi kapital, contoh: 'prospek' -> 'Prospek'
      $controllerName = ucwords($url[0]);
      if (file_exists('../app/controllers/' . $controllerName . '.php')) {
        $this->currentController = $controllerName;
        unset($url[0]);
      }
    }

    // Muat controller
    require_once '../app/controllers/' . $this->currentController . '.php';
    $this->currentController = new $this->currentController;

    // Cek method dari URL
    if (isset($url[1])) {
      if (method_exists($this->currentController, $url[1])) {
        $this->currentMethod = $url[1];
        unset($url[1]);
      }
    }

    // Ambil parameter
    $this->params = $url ? array_values($url) : [];

    // Panggil controller, method, dengan parameter
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
