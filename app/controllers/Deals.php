<?php
// app/controllers/Deals.php

// =======================================================
// == KELAS PDF KUSTOM DENGAN FOOTER ==
// =======================================================
require_once('../app/libraries/tcpdf/tcpdf.php');
class ProposalPDF extends TCPDF
{
  // Custom Footer untuk alamat perusahaan
  public function Footer()
  {
    $this->SetY(-25); // Posisi 25mm dari bawah
    $this->SetFont('helvetica', '', 8);
    $this->SetTextColor(100, 100, 100);
    // Garis pemisah
    $this->Line(15, $this->GetY(), 195, $this->GetY());
    $this->Ln(2);
    // Teks alamat
    $this->MultiCell(0, 4, "PT. Sriwijaya Internet Services\nJalan Pendawa Nomor 834, Kelurahan 2 Ilir, Kecamatan Ilir Timur II, Palembang, Sumatera Selatan 30118\nemail: office@sis.net.id", 0, 'C');
  }
  public function Header() {}
}


class Deals extends Controller
{
  private $dealModel;
  private $instansiModel;
  private $productModel;
  private $userModel;
  private $proposalModel;

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
    $this->proposalModel = $this->model('ProposalModel');
  }

  private function getUserScope()
  {
    $scope_type = 'all';
    $scope_value = null;
    $user_role_id = $_SESSION['user_role_id'];
    $user_id = $_SESSION['user_id'];
    $userModel = $this->model('User');
    $currentUser = $userModel->getUserById($user_id);
    $user_division_id = $currentUser->division_id ?? null;
    if (in_array($user_role_id, [3, 4, 5])) {
      $scope_type = 'division';
      $scope_value = $user_division_id;
    } elseif ($user_role_id == 6) {
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
    $params = [
      'search' => $_GET['search'] ?? '',
      'stage' => $_GET['stage'] ?? '',
      'limit' => $_GET['limit'] ?? 10,
      'offset' => (($_GET['page'] ?? 1) - 1) * ($_GET['limit'] ?? 10),
      'scope_type' => $scope['type'],
      'scope_value' => $scope['value']
    ];

    $deals = $this->dealModel->getDeals($params);
    $totalDeals = $this->dealModel->getTotalDeals($params);
    $totalPages = ($params['limit'] > 0) ? ceil($totalDeals / $params['limit']) : 1;

    // Mengambil semua peluang (tanpa paginasi) untuk modal
    $allDealsForActivity = $this->dealModel->getDeals(['scope_type' => $scope['type'], 'scope_value' => $scope['value']]);

    $data = [
      'title' => 'Manajemen Peluang',
      'deals' => $deals,
      'total_deals' => $totalDeals,
      'total_pages' => $totalPages,
      'current_page' => $_GET['page'] ?? 1,
      'limit' => $params['limit'],
      'search' => $params['search'],
      'stage' => $params['stage'],
      'all_deals_for_activity' => $allDealsForActivity // Kirim data peluang ke view
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
    $deals = $this->dealModel->getDeals(['scope_type' => $scope['type'], 'scope_value' => $scope['value']]);
    $dealsByStage = ['Analisis Kebutuhan' => [], 'Proposal' => [], 'Negosiasi' => [], 'Menang' => [], 'Kalah' => []];
    foreach ($deals as $deal) {
      if (array_key_exists($deal->stage, $dealsByStage)) {
        $dealsByStage[$deal->stage][] = $deal;
      }
    }
    $data = ['title' => 'Papan Kanban Kesepakatan', 'dealsByStage' => $dealsByStage];
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
        'value' => $_POST['value'],
        'stage' => $_POST['stage'],
        'expected_close_date' => !empty($_POST['expected_close_date']) ? $_POST['expected_close_date'] : null,
        'requirements_notes' => trim($_POST['requirements_notes'] ?? ''),
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
          header('Location: ' . BASE_URL . '/deals/edit/' . $id);
          exit;
        } else {
          flash('deal_message', 'Gagal mengupdate kesepakatan.', 'alert alert-danger');
        }
      }

      $deal = $this->dealModel->getDealById($id);
      $data['title'] = 'Edit Kesepakatan';
      $data['contacts'] = $this->instansiModel->getAllContactsWithCompanyName();
      $data['categories'] = $this->productModel->getAllCategories();
      $data['deal_products'] = $this->dealModel->getProductsByDealId($id);
      $data['proposal'] = $this->proposalModel->getProposalByDealId($id);
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
        'requirements_notes' => $deal->requirements_notes ?? '',
        'proposal' => $this->proposalModel->getProposalByDealId($id),
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
  }

  public function updateStage()
  {
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
      exit;
    }
    if (!can('update', 'deals')) {
      echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki izin.']);
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
      echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses.']);
      exit;
    }
    if ($this->dealModel->updateDealStage($dealId, $newStage)) {
      echo json_encode(['success' => true, 'message' => 'Stage berhasil diperbarui.']);
    } else {
      echo json_encode(['success' => false, 'message' => 'Gagal memperbarui stage.']);
    }
    exit;
  }

  public function updateProposal($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proposal'])) {
      $proposalData = $_POST['proposal'];
      $proposalData['deal_id'] = $id;

      if ($this->proposalModel->saveOrUpdateProposal($proposalData)) {
        flash('deal_message', 'Data proposal berhasil disimpan.');
        if (isset($_POST['action']) && $_POST['action'] == 'save_and_print') {
          header('Location: ' . BASE_URL . '/deals/generateProposalPdf/' . $id);
        } else {
          header('Location: ' . BASE_URL . '/deals/edit/' . $id);
        }
      } else {
        flash('deal_message', 'Gagal menyimpan data proposal.', 'alert alert-danger');
        header('Location: ' . BASE_URL . '/deals/edit/' . $id);
      }
    } else {
      header('Location: ' . BASE_URL . '/deals/edit/' . $id);
    }
    exit;
  }

  public function generateProposalPdf($deal_id)
  {
    $deal = $this->dealModel->getDealById($deal_id);
    $proposal = $this->proposalModel->getProposalByDealId($deal_id);

    if (!$deal || !$proposal) {
      die('Data tidak ditemukan.');
    }

    $pdf = new ProposalPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

    $pdf->SetCreator('Punyo CRM');
    $pdf->SetAuthor($deal->owner_name);
    $pdf->SetTitle('Proposal Penawaran - ' . $deal->name);

    // ================== HALAMAN 1: ISI PROPOSAL (TANPA COVER) ==================
    $pdf->AddPage();
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 30);

    $pdf->SetFont('helvetica', '', 10);
    $pdf->Image(ROOT . '/public/assets/images/sis.png', 15, 15, 50, '', 'PNG');
    $pdf->SetXY(15, 25);
    $pdf->Cell(0, 7, 'Palembang, ' . date('d F Y'), 0, 1, 'R');
    $pdf->Ln(15);

    $pdf->SetX(15);
    $pdf->Cell(25, 6, 'Nomor', 0, 0);
    $pdf->Cell(0, 6, ': ' . htmlspecialchars($proposal->proposal_number), 0, 1);
    $pdf->SetX(15);
    $pdf->Cell(25, 6, 'Lampiran', 0, 0);
    $pdf->Cell(0, 6, ': ' . htmlspecialchars($proposal->attachment), 0, 1);
    $pdf->SetX(15);
    $pdf->Cell(25, 6, 'Perihal', 0, 0);
    $pdf->Cell(0, 6, ': ' . htmlspecialchars($proposal->subject), 0, 1);
    $pdf->Ln(8);

    $pdf->SetX(15);
    $pdf->Cell(0, 6, 'Kepada Yth,', 0, 1);
    $pdf->SetX(15);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, htmlspecialchars($deal->company_name), 0, 1);
    $pdf->SetX(15);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Di Tempat', 0, 1);
    $pdf->Ln(8);

    $pdf->SetX(15);
    $pdf->MultiCell(180, 5, 'Dengan ini kami sampaikan ' . strtolower(htmlspecialchars($proposal->subject)) . ', sebagai berikut:', 0, 'J');
    $pdf->Ln(5);

    $html = file_get_contents(APPROOT . '/views/templates/proposal_table.html');

    $item_groups = ['barang' => '', 'jasa' => '', 'negosiasi' => '', 'diskon' => ''];
    $headers = ['jasa' => 'Jasa', 'negosiasi' => 'Negosiasi (Pengurang Biaya)', 'diskon' => 'Diskon (Pengurang Biaya)'];
    $header_classes = ['jasa' => 'sub-header', 'negosiasi' => 'sub-header-warning', 'diskon' => 'sub-header-danger'];
    $no_counter = [];

    foreach ($proposal->items as $item) {
      $type = $item->item_type;
      if (!isset($item_groups[$type])) continue;

      if (empty($item_groups[$type]) && $type != 'barang') {
        $item_groups[$type] .= '<tr><td colspan="6" class="' . $header_classes[$type] . '">' . $headers[$type] . '</td></tr>';
      }
      if (!isset($no_counter[$type])) {
        $no_counter[$type] = 1;
      }

      $total = $item->price * $item->quantity;
      $price_color = ($type == 'negosiasi' || $type == 'diskon') ? 'style="color:red;"' : '';
      $item_groups[$type] .= '<tr><td align="center">' . $no_counter[$type]++ . '</td><td>' . htmlspecialchars($item->description) . '</td><td align="right" ' . $price_color . '>' . number_format(abs($item->price), 0, ',', '.') . '</td><td align="center">' . $item->quantity . '</td><td align="center">' . htmlspecialchars($item->unit) . '</td><td align="right" ' . $price_color . '>' . number_format(abs($total), 0, ',', '.') . '</td></tr>';
    }

    $ppn = $proposal->subtotal * ($proposal->ppn_percentage / 100);
    $pph = $proposal->subtotal * ($proposal->pph_percentage / 100);

    $replacements = [
      '{{barang_items}}' => $item_groups['barang'],
      '{{jasa_items}}' => $item_groups['jasa'],
      '{{negosiasi_items}}' => $item_groups['negosiasi'],
      '{{diskon_items}}' => $item_groups['diskon'],
      '{{subtotal}}' => number_format($proposal->subtotal, 0, ',', '.'),
      '{{ppn_percent}}' => floatval($proposal->ppn_percentage),
      '{{ppn_value}}' => number_format($ppn, 0, ',', '.'),
      '{{pph_percent}}' => floatval($proposal->pph_percentage),
      '{{pph_value}}' => number_format($pph, 0, ',', '.'),
      '{{grandtotal}}' => number_format($proposal->grand_total, 0, ',', '.'),
    ];
    $html = str_replace(array_keys($replacements), array_values($replacements), $html);

    $pdf->writeHTMLCell(180, '', 15, $pdf->GetY(), $html, 0, 1, 0, true, '', true);
    $pdf->Ln(5);

    $pdf->SetX(15);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'Keterangan:', 0, 1);
    $pdf->SetX(15);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, '- Harga sudah termasuk pajak.', 0, 1);
    $pdf->Ln(5);
    $pdf->SetX(15);
    $pdf->MultiCell(180, 5, 'Demikianlah ' . strtolower(htmlspecialchars($proposal->subject)) . ' ini kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.', 0, 'J');

    $y_pos = $pdf->GetY();
    if ($y_pos > 220) {
      $pdf->AddPage();
      $pdf->SetY(20);
    } else {
      $pdf->Ln(5);
    }

    $pdf->SetX(15);
    $pdf->Cell(0, 6, 'Hormat kami,', 0, 1);
    $pdf->Ln(20);
    $pdf->SetX(15);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, '( ' . htmlspecialchars($deal->owner_name) . ' )', 0, 1);
    $pdf->SetX(15);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Account Manager', 0, 1);

    $pdf->Output('Proposal-' . $deal->name . '.pdf', 'I');
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
