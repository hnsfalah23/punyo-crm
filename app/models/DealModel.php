<?php
// app/models/DealModel.php

class DealModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  // Fungsi baru untuk mendapatkan detail error dari database
  public function getDbError()
  {
    return $this->db->getError();
  }

  private function buildWhereClause($params, &$bindings)
  {
    $sql = '';
    if (!empty($params['stage'])) {
      $sql .= ' AND d.stage = :stage';
      $bindings[':stage'] = $params['stage'];
    }
    if (!empty($params['search'])) {
      $sql .= ' AND (ct.name LIKE :search OR c.name LIKE :search OR d.name LIKE :search)';
      $bindings[':search'] = '%' . $params['search'] . '%';
    }
    if (isset($params['scope_type'])) {
      if ($params['scope_type'] == 'division') {
        $sql .= ' AND u.division_id = :division_id';
        $bindings[':division_id'] = $params['scope_value'];
      } elseif ($params['scope_type'] == 'self') {
        $sql .= ' AND d.owner_id = :owner_id';
        $bindings[':owner_id'] = $params['scope_value'];
      }
    }
    return $sql;
  }

  public function getDeals($params = [])
  {
    $bindings = [];
    $whereClause = $this->buildWhereClause($params, $bindings);

    $sql = "
      SELECT 
        d.*, 
        c.name as company_name, 
        u.name as owner_name,
        ct.name as contact_name,
        GROUP_CONCAT(DISTINCT p.name SEPARATOR ', ') as product_names
      FROM deals as d
      JOIN companies as c ON d.company_id = c.company_id
      JOIN users as u ON d.owner_id = u.user_id
      JOIN contacts as ct ON d.contact_id = ct.contact_id
      LEFT JOIN deal_products as dp ON d.deal_id = dp.deal_id
      LEFT JOIN products as p ON dp.product_id = p.product_id
      WHERE 1=1 {$whereClause}
      GROUP BY d.deal_id
      ORDER BY d.created_at DESC
    ";

    if (isset($params['limit']) && isset($params['offset'])) {
      $sql .= ' LIMIT :limit OFFSET :offset';
      $bindings[':limit'] = (int) $params['limit'];
      $bindings[':offset'] = (int) $params['offset'];
    }

    $this->db->query($sql);
    foreach ($bindings as $key => &$val) {
      $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
      $this->db->bind($key, $val, $type);
    }

    return $this->db->resultSet();
  }

  public function getTotalDeals($params = [])
  {
    $bindings = [];
    $whereClause = $this->buildWhereClause($params, $bindings);

    $sql = "
      SELECT COUNT(DISTINCT d.deal_id) as total 
      FROM deals as d
      JOIN companies as c ON d.company_id = c.company_id
      JOIN users as u ON d.owner_id = u.user_id
      JOIN contacts as ct ON d.contact_id = ct.contact_id
      WHERE 1=1 {$whereClause}
    ";

    $this->db->query($sql);
    foreach ($bindings as $key => &$val) {
      $this->db->bind($key, $val);
    }

    $result = $this->db->single();
    return $result ? $result->total : 0;
  }

  public function getDealById($id)
  {
    $this->db->query('
      SELECT 
        d.*, 
        c.name as company_name, 
        c.company_id,
        u.name as owner_name, 
        u.division_id as owner_division_id,
        ct.name as contact_name,
        ct.email as contact_email,
        ct.phone as contact_phone
      FROM deals as d
      JOIN companies as c ON d.company_id = c.company_id
      JOIN users as u ON d.owner_id = u.user_id
      JOIN contacts as ct ON d.contact_id = ct.contact_id
      WHERE d.deal_id = :id
    ');
    $this->db->bind(':id', $id);
    return $this->db->single();
  }

  public function getProductsByDealId($id)
  {
    $this->db->query('
      SELECT 
        p.*, 
        dp.quantity, 
        dp.price_per_unit,
        (dp.quantity * dp.price_per_unit) as total_price
      FROM deal_products as dp
      JOIN products as p ON dp.product_id = p.product_id
      WHERE dp.deal_id = :id
      ORDER BY p.name ASC
    ');
    $this->db->bind(':id', $id);
    return $this->db->resultSet();
  }

  public function addDeal($data)
  {
    $this->db->query('
      INSERT INTO deals (name, stage, value, owner_id, company_id, contact_id, expected_close_date, created_at, updated_at) 
      VALUES (:name, :stage, :value, :owner_id, :company_id, :contact_id, :expected_close_date, NOW(), NOW())
    ');
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':stage', $data['stage']);
    $this->db->bind(':value', $data['value']);
    $this->db->bind(':owner_id', $data['owner_id']);
    $this->db->bind(':company_id', $data['company_id']);
    $this->db->bind(':contact_id', $data['contact_id']);
    $this->db->bind(':expected_close_date', $data['expected_close_date']);

    if ($this->db->execute()) {
      return $this->db->lastInsertId();
    }
    return false;
  }

  public function updateDeal($data)
  {
    $this->db->query('
      UPDATE deals SET 
        name = :name, stage = :stage, value = :value, 
        contact_id = :contact_id, expected_close_date = :expected_close_date, updated_at = NOW()
      WHERE deal_id = :id
    ');
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':stage', $data['stage']);
    $this->db->bind(':value', $data['value']);
    $this->db->bind(':contact_id', $data['contact_id']);
    $this->db->bind(':expected_close_date', $data['expected_close_date']);
    return $this->db->execute();
  }

  public function deleteDeal($id)
  {
    $this->db->beginTransaction();
    try {
      $this->removeProductsFromDeal($id);
      $this->db->query('DELETE FROM deals WHERE deal_id = :id');
      $this->db->bind(':id', $id);
      $this->db->execute();
      $this->db->commit();
      return true;
    } catch (Exception $e) {
      $this->db->rollback();
      error_log('Error deleting deal: ' . $e->getMessage());
      return false;
    }
  }

  public function addMultipleProductsToDeal($deal_id, $products)
  {
    if (empty($products)) return true;
    $this->db->beginTransaction();
    try {
      foreach ($products as $product) {
        $this->db->query('INSERT INTO deal_products (deal_id, product_id, quantity, price_per_unit) VALUES (:deal_id, :product_id, :quantity, :price)');
        $this->db->bind(':deal_id', $deal_id);
        $this->db->bind(':product_id', $product['id']);
        $this->db->bind(':quantity', $product['quantity']);
        $this->db->bind(':price', $product['price']);
        $this->db->execute();
      }
      $this->db->commit();
      return true;
    } catch (Exception $e) {
      $this->db->rollback();
      error_log('Error adding multiple products to deal: ' . $e->getMessage());
      return false;
    }
  }

  public function removeProductsFromDeal($deal_id)
  {
    $this->db->query('DELETE FROM deal_products WHERE deal_id = :deal_id');
    $this->db->bind(':deal_id', $deal_id);
    return $this->db->execute();
  }

  public function updateDealStage($deal_id, $new_stage)
  {
    $allowed_stages = ['Analisis Kebutuhan', 'Proposal', 'Negosiasi', 'Menang', 'Kalah'];
    if (!in_array($new_stage, $allowed_stages)) {
      return false;
    }
    $this->db->query('UPDATE deals SET stage = :stage, updated_at = NOW() WHERE deal_id = :id');
    $this->db->bind(':stage', $new_stage);
    $this->db->bind(':id', $deal_id);
    return $this->db->execute();
  }

  public function checkDealAccess($deal_id, $user_id, $user_role_id = null, $user_division_id = null)
  {
    $this->db->query('SELECT d.owner_id, u.division_id as owner_division_id FROM deals as d JOIN users as u ON d.owner_id = u.user_id WHERE d.deal_id = :deal_id');
    $this->db->bind(':deal_id', $deal_id);
    $deal = $this->db->single();

    if (!$deal) return false;
    if (in_array($user_role_id, [1, 2])) return true; // Super Admin & Admin
    if (in_array($user_role_id, [3, 4, 5]) && $deal->owner_division_id == $user_division_id) return true; // Manajer/SPV
    if ($user_role_id == 6 && $deal->owner_id == $user_id) return true; // Staf

    return false;
  }
}
