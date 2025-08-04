<?php
// app/models/LeadModel.php

class LeadModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  /**
   * Fungsi bantuan privat untuk membangun klausa WHERE secara dinamis
   */
  private function buildWhereClause($params, &$bindings)
  {
    $sql = '';
    if (!empty($params['status'])) {
      $sql .= ' AND l.status = :status';
      $bindings[':status'] = $params['status'];
    }
    if (!empty($params['search'])) {
      $sql .= ' AND (l.name LIKE :search OR l.company_name LIKE :search)';
      $bindings[':search'] = '%' . $params['search'] . '%';
    }
    // Logika filter berdasarkan peran pengguna (FIX UTAMA DI SINI)
    if (isset($params['scope_type'])) {
      if ($params['scope_type'] == 'division') {
        $sql .= ' AND u.division_id = :division_id';
        $bindings[':division_id'] = $params['scope_value'];
      } elseif ($params['scope_type'] == 'self') {
        $sql .= ' AND l.owner_id = :owner_id';
        $bindings[':owner_id'] = $params['scope_value'];
      }
    }
    return $sql;
  }

  public function getLeads($params = [])
  {
    $bindings = [];
    $whereClause = $this->buildWhereClause($params, $bindings);

    $sql = "
        SELECT l.*, u.name as owner_name, u.profile_picture as owner_photo
        FROM leads as l
        JOIN users as u ON l.owner_id = u.user_id
        WHERE 1=1 {$whereClause}
        ORDER BY l.created_at DESC
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

  public function getTotalLeads($params = [])
  {
    $bindings = [];
    $whereClause = $this->buildWhereClause($params, $bindings);

    $sql = "
        SELECT COUNT(l.lead_id) as total 
        FROM leads as l 
        JOIN users as u ON l.owner_id = u.user_id 
        WHERE 1=1 {$whereClause}
    ";

    $this->db->query($sql);
    foreach ($bindings as $key => &$val) {
      $this->db->bind($key, $val);
    }

    $result = $this->db->single();
    return $result ? $result->total : 0;
  }

  public function getLeadById($id)
  {
    $this->db->query('
            SELECT l.*, u.name as owner_name
            FROM leads as l
            JOIN users as u ON l.owner_id = u.user_id
            WHERE l.lead_id = :id
        ');
    $this->db->bind(':id', $id);
    return $this->db->single();
  }

  public function addLead($data)
  {
    $this->db->query('INSERT INTO leads (name, status, owner_id, source, company_name, email, phone) VALUES (:name, :status, :owner_id, :source, :company_name, :email, :phone)');
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':status', $data['status']);
    $this->db->bind(':owner_id', $data['owner_id']);
    $this->db->bind(':source', $data['source']);
    $this->db->bind(':company_name', $data['company_name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':phone', $data['phone']);
    return $this->db->execute();
  }

  public function updateLead($data)
  {
    $this->db->query('UPDATE leads SET name = :name, status = :status, source = :source, company_name = :company_name, email = :email, phone = :phone WHERE lead_id = :id');
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':status', $data['status']);
    $this->db->bind(':source', $data['source']);
    $this->db->bind(':company_name', $data['company_name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':phone', $data['phone']);
    return $this->db->execute();
  }

  public function deleteLead($id)
  {
    $this->db->query('DELETE FROM leads WHERE lead_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }
}
