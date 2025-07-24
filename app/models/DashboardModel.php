<?php
// app/models/DashboardModel.php

class DashboardModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  private function buildScopeWhereClause($scope_type, $scope_value, $user_alias, $owner_alias)
  {
    if ($scope_type == 'division') {
      return " AND $user_alias.division_id = " . (int)$scope_value;
    } elseif ($scope_type == 'self') {
      return " AND $owner_alias.owner_id = " . (int)$scope_value;
    }
    return ''; // 'all' scope
  }

  public function getDashboardStats($scope_type, $scope_value)
  {
    $stats = [];
    $scopeWhereLeads = $this->buildScopeWhereClause($scope_type, $scope_value, 'u', 'l');
    $scopeWhereDeals = $this->buildScopeWhereClause($scope_type, $scope_value, 'u', 'd');
    $scopeWhereActivities = $this->buildScopeWhereClause($scope_type, $scope_value, 'u', 'a');

    $this->db->query("SELECT COUNT(l.lead_id) as total FROM leads l JOIN users u ON l.owner_id = u.user_id WHERE 1=1 $scopeWhereLeads");
    $stats['total_leads'] = $this->db->single()->total;

    $this->db->query("SELECT COUNT(d.deal_id) as total FROM deals d JOIN users u ON d.owner_id = u.user_id WHERE d.stage NOT IN ('Menang', 'Kalah') $scopeWhereDeals");
    $stats['total_ongoing_deals'] = $this->db->single()->total;

    // Diperbaiki: Tambahkan statistik untuk deal baru bulan ini
    $this->db->query("SELECT COUNT(d.deal_id) as total FROM deals d JOIN users u ON d.owner_id = u.user_id WHERE MONTH(d.created_at) = MONTH(CURDATE()) AND YEAR(d.created_at) = YEAR(CURDATE()) $scopeWhereDeals");
    $stats['monthly_new_deals'] = $this->db->single()->total;

    $this->db->query("SELECT SUM(d.value) as total FROM deals d JOIN users u ON d.owner_id = u.user_id WHERE d.stage = 'Menang' AND MONTH(d.created_at) = MONTH(CURDATE()) AND YEAR(d.created_at) = YEAR(CURDATE()) $scopeWhereDeals");
    $stats['monthly_revenue'] = $this->db->single()->total ?? 0;

    $this->db->query("SELECT COUNT(a.activity_id) as total FROM activities a JOIN users u ON a.owner_id = u.user_id WHERE MONTH(a.start_time) = MONTH(CURDATE()) AND YEAR(a.start_time) = YEAR(CURDATE()) $scopeWhereActivities");
    $stats['monthly_activities'] = $this->db->single()->total;

    return $stats;
  }

  public function getSalesFunnelData($scope_type, $scope_value)
  {
    $scopeWhereLeads = $this->buildScopeWhereClause($scope_type, $scope_value, 'u', 'l');
    $scopeWhereDeals = $this->buildScopeWhereClause($scope_type, $scope_value, 'u', 'd');

    $this->db->query("
            SELECT 
                (SELECT COUNT(l.lead_id) FROM leads l JOIN users u ON l.owner_id = u.user_id WHERE 1=1 $scopeWhereLeads) as total_leads,
                (SELECT COUNT(d.deal_id) FROM deals d JOIN users u ON d.owner_id = u.user_id WHERE 1=1 $scopeWhereDeals) as total_deals,
                (SELECT COUNT(d.deal_id) FROM deals d JOIN users u ON d.owner_id = u.user_id WHERE d.stage = 'Menang' $scopeWhereDeals) as deals_won
        ");
    return $this->db->single();
  }

  public function getTargetAchievementData($scope_type, $scope_value, $monthly_revenue)
  {
    $scopeWhere = '';
    if ($scope_type == 'division') $scopeWhere = " AND u.division_id = " . (int)$scope_value;
    elseif ($scope_type == 'self') $scopeWhere = " AND t.user_id = " . (int)$scope_value;

    $currentMonth = date('Y-m');

    // Perbaikan: Ubah t.target_value menjadi t.value
    $this->db->query("SELECT SUM(t.value) as total FROM targets t JOIN users u ON t.user_id = u.user_id WHERE t.target_month = :month AND t.target_type = 'penjualan' $scopeWhere");
    $this->db->bind(':month', $currentMonth);
    $target = $this->db->single()->total ?? 0;

    return ['target' => $target, 'achieved' => $monthly_revenue];
  }

  public function getTargetDealCountData($scope_type, $scope_value, $monthly_new_deals)
  {
    $scopeWhere = '';
    if ($scope_type == 'division') $scopeWhere = " AND u.division_id = " . (int)$scope_value;
    elseif ($scope_type == 'self') $scopeWhere = " AND t.user_id = " . (int)$scope_value;

    $currentMonth = date('Y-m');

    // Perbaikan: Ubah t.target_value menjadi t.value
    $this->db->query("SELECT SUM(t.value) as total FROM targets t JOIN users u ON t.user_id = u.user_id WHERE t.target_month = :month AND t.target_type = 'jumlah_deal' $scopeWhere");
    $this->db->bind(':month', $currentMonth);
    $target = $this->db->single()->total ?? 0;

    return ['target' => $target, 'achieved' => $monthly_new_deals];
  }

  public function getDealsByStageData($scope_type, $scope_value)
  {
    $scopeWhere = $this->buildScopeWhereClause($scope_type, $scope_value, 'u', 'd');
    $this->db->query("
            SELECT stage, COUNT(deal_id) as count
            FROM deals d
            JOIN users u ON d.owner_id = u.user_id
            WHERE 1=1 $scopeWhere
            GROUP BY stage
        ");
    return $this->db->resultSet();
  }
}
