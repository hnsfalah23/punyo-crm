<?php
// app/models/ReportModel.php

class ReportModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  public function getTeamPerformance($startDate, $endDate)
  {
    $this->db->query('
            SELECT
                u.name as user_name,
                COUNT(d.deal_id) as total_deals_won,
                SUM(d.value) as total_revenue
            FROM deals as d
            JOIN users as u ON d.owner_id = u.user_id
            WHERE d.stage = "Menang" AND d.created_at BETWEEN :start_date AND :end_date
            GROUP BY u.user_id, u.name
            ORDER BY total_revenue DESC
        ');
    $this->db->bind(':start_date', $startDate . ' 00:00:00');
    $this->db->bind(':end_date', $endDate . ' 23:59:59');
    return $this->db->resultSet();
  }

  public function getLeadConversion($startDate, $endDate)
  {
    $this->db->query("
            SELECT 
                status, 
                COUNT(lead_id) as count 
            FROM leads 
            WHERE status IN ('Terkualifikasi', 'Gagal') 
            AND created_at BETWEEN :start_date AND :end_date
            GROUP BY status
        ");
    $this->db->bind(':start_date', $startDate . ' 00:00:00');
    $this->db->bind(':end_date', $endDate . ' 23:59:59');
    $results = $this->db->resultSet();

    $data = ['Terkualifikasi' => 0, 'Gagal' => 0];
    foreach ($results as $row) {
      $data[$row->status] = $row->count;
    }
    return $data;
  }

  public function getSalesByProduct($startDate, $endDate)
  {
    $this->db->query("
            SELECT 
                p.name as product_name,
                SUM(dp.quantity) as total_quantity,
                SUM(dp.quantity * dp.price_per_unit) as total_revenue
            FROM deal_products dp
            JOIN products p ON dp.product_id = p.product_id
            JOIN deals d ON dp.deal_id = d.deal_id
            WHERE d.stage = 'Menang' AND d.created_at BETWEEN :start_date AND :end_date
            GROUP BY p.product_id, p.name
            ORDER BY total_revenue DESC
        ");
    $this->db->bind(':start_date', $startDate . ' 00:00:00');
    $this->db->bind(':end_date', $endDate . ' 23:59:59');
    return $this->db->resultSet();
  }
}
