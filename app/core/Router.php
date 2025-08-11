<?php
// app/core/Router.php

class Router
{
  protected $currentController = 'Dashboard'; // Controller default
  protected $currentMethod = 'index';
  protected $params = [];

  private $aliases = [
    'produk' => 'Products',
    'pengguna' => 'Users',
    'aktivitas' => 'Activities',
    'peluang' => 'Peluang',
    'prospek' => 'Prospek',
    'instansi' => 'Instansi',
    'reports' => 'Reports',
    'targets' => 'Targets',
    'permissions' => 'Permissions',
    'auth' => 'Auth',
    'dashboard' => 'Dashboard'
  ];

  public function __construct()
  {
    $url = $this->getUrl();

    // Cek controller dari URL
    if (isset($url[0]) && $url[0] !== '') {
      $first = $url[0];
      $controllerName = $this->aliases[$first] ?? ucwords($first);
      if (file_exists(APPROOT . '/controllers/' . $controllerName . '.php')) {
        $this->currentController = $controllerName;
        unset($url[0]);
      } else {
        return $this->render404();
      }
    }

    // Muat controller
    require_once APPROOT . '/controllers/' . $this->currentController . '.php';
    $this->currentController = new $this->currentController;

    // Cek method dari URL
    if (isset($url[1]) && $url[1] !== '') {
      if (method_exists($this->currentController, $url[1])) {
        $this->currentMethod = $url[1];
        unset($url[1]);
      } else {
        return $this->render404();
      }
    }

    // Ambil parameter
    $this->params = $url ? array_values($url) : [];

    // Panggil controller, method, dengan parameter
    call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
  }

  private function render404()
  {
    http_response_code(404);
    echo '404 Not Found';
    exit;
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
