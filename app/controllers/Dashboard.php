<?php
// app/controllers/Dashboard.php

class Dashboard extends Controller
{
  private $dashboardModel;

  public function __construct()
  {
    if (!isLoggedIn()) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    $this->dashboardModel = $this->model('DashboardModel');
  }

  public function index()
  {
    $scope_type = 'all';
    $scope_value = null;
    $user_role_id = $_SESSION['user_role_id'];
    $user_id = $_SESSION['user_id'];

    $userModel = $this->model('User');
    $currentUser = $userModel->getUserById($user_id);
    $user_division_id = $currentUser->division_id ?? null;

    if (in_array($user_role_id, [3, 4, 5])) { // Manajer, SPV
      $scope_type = 'division';
      $scope_value = $user_division_id;
    } elseif ($user_role_id == 6) { // Staf
      $scope_type = 'self';
      $scope_value = $user_id;
    }

    $stats = $this->dashboardModel->getDashboardStats($scope_type, $scope_value);
    $funnelData = $this->dashboardModel->getSalesFunnelData($scope_type, $scope_value);
    $targetRevenueData = $this->dashboardModel->getTargetAchievementData($scope_type, $scope_value, $stats['monthly_revenue']);
    $targetDealCountData = $this->dashboardModel->getTargetDealCountData($scope_type, $scope_value, $stats['monthly_new_deals']);
    $dealsByStage = $this->dashboardModel->getDealsByStageData($scope_type, $scope_value);

    $data = [
      'title' => 'Dashboard',
      'stats' => $stats,
      'funnelData' => $funnelData,
      'targetRevenueData' => $targetRevenueData,
      'targetDealCountData' => $targetDealCountData,
      'dealsByStage' => $dealsByStage
    ];

    $this->renderView('pages/dashboard/index', $data);
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
