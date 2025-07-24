<?php
// app/controllers/Deals.php

class Deals extends Controller
{
  private $dealModel;
  private $instansiModel;
  private $productModel;
  private $userModel;

  public function __construct()
  {
    if (!isLoggedIn()) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    $this->dealModel = $this->model('DealModel');
    $this->instansiModel = $this->model('InstansiModel');
    $this->productModel = $this->model('Product');
    $this->userModel = $this->model('User');
  }

  private function getUserScope()
  {
    $scope_type = 'all';
    $scope_value = null;
    $user_role_id = $_SESSION['user_role_id'];
    $user_id = $_SESSION['user_id'];

    $currentUser = $this->userModel->getUserById($user_id);
    $user_division_id = $currentUser->division_id ?? null;

    if (in_array($user_role_id, [3, 4, 5])) { // Manajer, SPV
      $scope_type = 'division';
      $scope_value = $user_division_id;
    } elseif ($user_role_id == 6) { // Staf
      $scope_type = 'self';
      $scope_value = $user_id;
    }
    return ['type' => $scope_type, 'value' => $scope_value];
  }

  public function index()
  {
    if (!can('read', 'deals')) {
      flash('dashboard_message', 'Anda tidak memiliki hak akses.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }

    $scope = $this->getUserScope();
    $search = $_GET['search'] ?? '';
    $stage = $_GET['stage'] ?? '';
    $limit = $_GET['limit'] ?? 10;
    $page = $_GET['page'] ?? 1;

    $params = [
      'search' => $search,
      'stage' => $stage,
      'limit' => $limit,
      'offset' => ($page - 1) * $limit,
      'scope_type' => $scope['type'],
      'scope_value' => $scope['value']
    ];

    $deals = $this->dealModel->getDeals($params);
    $totalDeals = $this->dealModel->getTotalDeals($params);
    $totalPages = ($limit > 0) ? ceil($totalDeals / $limit) : 1;

    $data = [
      'title' => 'Manajemen Kesepakatan',
      'deals' => $deals,
      'total_deals' => $totalDeals,
      'total_pages' => $totalPages,
      'current_page' => $page,
      'limit' => $limit,
      'search' => $search,
      'stage' => $stage
    ];
    $this->renderView('pages/deals/index', $data);
  }

  public function kanban()
  {
    if (!can('read', 'deals')) {
      flash('dashboard_message', 'Anda tidak memiliki hak akses.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }

    $scope = $this->getUserScope();
    $params = [
      'scope_type' => $scope['type'],
      'scope_value' => $scope['value']
    ];

    $deals = $this->dealModel->getDeals($params);
    $dealsByStage = [
      'Analisis Kebutuhan' => [],
      'Proposal' => [],
      'Negosiasi' => [],
      'Menang' => [],
      'Kalah' => []
    ];

    foreach ($deals as $deal) {
      if (array_key_exists($deal->stage, $dealsByStage)) {
        $dealsByStage[$deal->stage][] = $deal;
      }
    }

    $data = [
      'title' => 'Papan Kanban Kesepakatan',
      'dealsByStage' => $dealsByStage
    ];
    $this->renderView('pages/deals/kanban', $data);
  }

  public function add()
  {
    if (!can('create', 'deals')) {
      header('Location: ' . BASE_URL . '/deals');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $contact = $this->instansiModel->getContactById($_POST['contact_id']);
      $products_in_deal = [];
      if (isset($_POST['products'])) {
        foreach ($_POST['products'] as $product_data) {
          $products_in_deal[] = [
            'id' => $product_data['id'],
            'quantity' => $product_data['quantity'],
            'price' => $product_data['price']
          ];
        }
      }
      $data = [
        'name' => trim($_POST['name']),
        'contact_id' => $_POST['contact_id'],
        'value' => trim($_POST['value']),
        'stage' => $_POST['stage'],
        'expected_close_date' => !empty($_POST['expected_close_date']) ? $_POST['expected_close_date'] : null,
        'products_in_deal' => $products_in_deal,
        'owner_id' => $_SESSION['user_id'],
        'contact_id_err' => ''
      ];

      if (!$contact) {
        $data['contact_id_err'] = 'Kontak yang dipilih tidak valid.';
      } else {
        $data['company_id'] = $contact->company_id;
      }

      if (empty($data['contact_id_err'])) {
        $newDealId = $this->dealModel->addDeal($data);
        if ($newDealId) {
          if (!empty($data['products_in_deal'])) {
            $this->dealModel->addMultipleProductsToDeal($newDealId, $data['products_in_deal']);
          }
          flash('deal_message', 'Kesepakatan baru berhasil ditambahkan.');
          header('Location: ' . BASE_URL . '/deals');
          exit;
        } else {
          flash('deal_message', 'Gagal menambahkan kesepakatan.', 'alert alert-danger');
        }
      }

      $data['title'] = 'Tambah Kesepakatan';
      $data['contacts'] = $this->instansiModel->getAllContactsWithCompanyName();
      $data['categories'] = $this->productModel->getAllCategories();
      $this->renderView('pages/deals/add', $data);
    } else {
      $data = [
        'title' => 'Tambah Kesepakatan',
        'contacts' => $this->instansiModel->getAllContactsWithCompanyName(),
        'categories' => $this->productModel->getAllCategories(),
        'name' => '',
        'contact_id' => '',
        'value' => 0,
        'stage' => 'Analisis Kebutuhan',
        'expected_close_date' => '',
        'name_err' => '',
        'contact_id_err' => ''
      ];
      $this->renderView('pages/deals/add', $data);
    }
  }

  public function edit($id)
  {
    if (!can('update', 'deals')) {
      header('Location: ' . BASE_URL . '/deals');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $products_in_deal = [];
      if (isset($_POST['products'])) {
        foreach ($_POST['products'] as $product_data) {
          $products_in_deal[] = [
            'id' => $product_data['id'],
            'quantity' => $product_data['quantity'],
            'price' => $product_data['price']
          ];
        }
      }
      $data = [
        'id' => $id,
        'name' => trim($_POST['name']),
        'contact_id' => $_POST['contact_id'],
        'value' => trim($_POST['value']),
        'stage' => $_POST['stage'],
        'expected_close_date' => !empty($_POST['expected_close_date']) ? $_POST['expected_close_date'] : null,
        'products_in_deal' => $products_in_deal,
        'contact_id_err' => ''
      ];

      if (empty($data['contact_id'])) {
        $data['contact_id_err'] = 'Kontak harus dipilih.';
      }

      if (empty($data['contact_id_err'])) {
        if ($this->dealModel->updateDeal($data)) {
          $this->dealModel->removeProductsFromDeal($id);
          if (!empty($data['products_in_deal'])) {
            $this->dealModel->addMultipleProductsToDeal($id, $data['products_in_deal']);
          }
          flash('deal_message', 'Kesepakatan berhasil diupdate.');
          header('Location: ' . BASE_URL . '/deals');
          exit;
        } else {
          flash('deal_message', 'Gagal mengupdate kesepakatan.', 'alert alert-danger');
        }
      }

      $data['title'] = 'Edit Kesepakatan';
      $data['contacts'] = $this->instansiModel->getAllContactsWithCompanyName();
      $data['categories'] = $this->productModel->getAllCategories();
      $data['deal_products'] = $this->dealModel->getProductsByDealId($id);
      $this->renderView('pages/deals/edit', $data);
    } else {
      $deal = $this->dealModel->getDealById($id);
      if (!$deal) {
        header('Location: ' . BASE_URL . '/deals');
        exit;
      }
      $data = [
        'title' => 'Edit Kesepakatan',
        'id' => $id,
        'contacts' => $this->instansiModel->getAllContactsWithCompanyName(),
        'categories' => $this->productModel->getAllCategories(),
        'deal_products' => $this->dealModel->getProductsByDealId($id),
        'name' => $deal->name,
        'contact_id' => $deal->contact_id,
        'value' => $deal->value,
        'stage' => $deal->stage,
        'expected_close_date' => $deal->expected_close_date,
        'name_err' => '',
        'contact_id_err' => ''
      ];
      $this->renderView('pages/deals/edit', $data);
    }
  }

  public function detail($id)
  {
    if (!can('read', 'deals')) {
      header('Location: ' . BASE_URL . '/deals');
      exit;
    }
    $deal = $this->dealModel->getDealById($id);
    if (!$deal) {
      header('Location: ' . BASE_URL . '/deals');
      exit;
    }

    $activityModel = $this->model('ActivityModel');
    $data = [
      'title' => 'Detail Kesepakatan',
      'deal' => $deal,
      'products' => $this->dealModel->getProductsByDealId($id),
      'activities' => $activityModel->getActivitiesByItemId($id, 'deal')
    ];
    $this->renderView('pages/deals/detail', $data);
  }

  public function delete($id)
  {
    if (!can('delete', 'deals')) {
      header('Location: ' . BASE_URL . '/deals');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($this->dealModel->deleteDeal($id)) {
        flash('deal_message', 'Kesepakatan berhasil dihapus.');
      } else {
        flash('deal_message', 'Gagal menghapus kesepakatan.', 'alert alert-danger');
      }
      header('Location: ' . BASE_URL . '/deals');
      exit;
    }
    header('Location: ' . BASE_URL . '/deals');
    exit;
  }

  public function updateStage()
  {
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
      exit;
    }

    if (!can('update', 'deals')) {
      echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengupdate.']);
      exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $dealId = $input['deal_id'] ?? null;
    $newStage = $input['stage'] ?? null;

    if (!$dealId || !$newStage) {
      echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
      exit;
    }

    $scope = $this->getUserScope();
    if (!$this->dealModel->checkDealAccess($dealId, $_SESSION['user_id'], $_SESSION['user_role_id'], $scope['value'])) {
      echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke deal ini.']);
      exit;
    }

    if ($this->dealModel->updateDealStage($dealId, $newStage)) {
      echo json_encode(['success' => true, 'message' => 'Stage berhasil diperbarui.']);
    } else {
      // **PERUBAHAN UTAMA DI SINI**
      // Ambil pesan error dari model untuk ditampilkan
      $db_error = $this->dealModel->getDbError();
      $response_message = 'Gagal memperbarui stage di database.';
      if ($db_error) {
        // Kirim detail error untuk debugging (HANYA UNTUK DEVELOPMENT)
        $response_message .= ' Detail: ' . $db_error;
      }
      echo json_encode(['success' => false, 'message' => $response_message]);
    }
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
