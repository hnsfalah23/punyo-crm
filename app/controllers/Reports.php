<?php
// app/controllers/Reports.php

class Reports extends Controller
{
  private $reportModel;

  public function __construct()
  {
    if (!isLoggedIn()) { /* ... */
    }
    if (!can('read', 'reports')) { /* ... */
    }
    $this->reportModel = $this->model('ReportModel');
  }

  public function index()
  {
    $data = ['title' => 'Pilih Laporan'];
    $this->renderView('pages/reports/index', $data);
  }

  public function team_performance()
  {
    $startDate = $_POST['start_date'] ?? date('Y-m-01');
    $endDate = $_POST['end_date'] ?? date('Y-m-t');

    $performanceData = $this->reportModel->getTeamPerformance($startDate, $endDate);
    $data = [
      'title' => 'Laporan Kinerja Tim',
      'performance_data' => $performanceData,
      'start_date' => $startDate,
      'end_date' => $endDate
    ];
    $this->renderView('pages/reports/team_performance', $data);
  }

  public function lead_conversion()
  {
    $startDate = $_POST['start_date'] ?? date('Y-m-01');
    $endDate = $_POST['end_date'] ?? date('Y-m-t');

    $conversionData = $this->reportModel->getLeadConversion($startDate, $endDate);
    $data = [
      'title' => 'Laporan Konversi Prospek',
      'conversion_data' => $conversionData,
      'start_date' => $startDate,
      'end_date' => $endDate
    ];
    $this->renderView('pages/reports/lead_conversion', $data);
  }

  public function sales_by_product()
  {
    $startDate = $_POST['start_date'] ?? date('Y-m-01');
    $endDate = $_POST['end_date'] ?? date('Y-m-t');

    $salesData = $this->reportModel->getSalesByProduct($startDate, $endDate);
    $data = [
      'title' => 'Laporan Penjualan per Produk',
      'sales_data' => $salesData,
      'start_date' => $startDate,
      'end_date' => $endDate
    ];
    $this->renderView('pages/reports/sales_by_product', $data);
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
