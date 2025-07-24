<?php
// app/models/LeadModel.php

class LeadModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  public function getLeads($params = [])
  {
    $sql = '
            SELECT l.*, u.name as owner_name 
            FROM leads as l
            JOIN users as u ON l.owner_id = u.user_id
            WHERE 1=1
        ';

    if (!empty($params['status'])) {
      $sql .= ' AND l.status = :status';
    }
    if (!empty($params['search'])) {
      $sql .= ' AND (l.name LIKE :search OR l.company_name LIKE :search)';
    }
    if (!empty($params['scope_ids'])) {
      $placeholders = implode(',', array_map('intval', $params['scope_ids']));
      $sql .= " AND l.owner_id IN ($placeholders)";
    }

    $sql .= ' ORDER BY l.created_at DESC';

    if (isset($params['limit']) && isset($params['offset'])) {
      $sql .= ' LIMIT :limit OFFSET :offset';
    }

    $this->db->query($sql);

    if (!empty($params['status'])) $this->db->bind(':status', $params['status']);
    if (!empty($params['search'])) $this->db->bind(':search', '%' . $params['search'] . '%');
    if (isset($params['limit'])) {
      $this->db->bind(':limit', $params['limit'], PDO::PARAM_INT);
      $this->db->bind(':offset', $params['offset'], PDO::PARAM_INT);
    }

    return $this->db->resultSet();
  }

  public function getTotalLeads($params = [])
  {
    $sql = 'SELECT COUNT(l.lead_id) as total FROM leads as l JOIN users as u ON l.owner_id = u.user_id WHERE 1=1';

    if (!empty($params['status'])) {
      $sql .= ' AND l.status = :status';
    }
    if (!empty($params['search'])) {
      $sql .= ' AND (l.name LIKE :search OR l.company_name LIKE :search)';
    }
    if (!empty($params['scope_ids'])) {
      $placeholders = implode(',', array_map('intval', $params['scope_ids']));
      $sql .= " AND l.owner_id IN ($placeholders)";
    }

    $this->db->query($sql);

    if (!empty($params['status'])) $this->db->bind(':status', $params['status']);
    if (!empty($params['search'])) $this->db->bind(':search', '%' . $params['search'] . '%');

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
