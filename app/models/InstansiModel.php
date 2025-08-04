<?php
// app/models/InstansiModel.php

class InstansiModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  private function buildWhereClause($params, &$bindings)
  {
    $sql = '';
    if (!empty($params['search'])) {
      $sql .= ' AND (c.name LIKE :search OR c.industry LIKE :search)';
      $bindings[':search'] = '%' . $params['search'] . '%';
    }
    if (!empty($params['filter_industry'])) {
      $sql .= ' AND c.industry = :industry';
      $bindings[':industry'] = $params['filter_industry'];
    }
    return $sql;
  }

  public function getInstansi($params = [])
  {
    $bindings = [];
    $whereClause = $this->buildWhereClause($params, $bindings);

    $sql = "
      SELECT 
        c.*, 
        GROUP_CONCAT(DISTINCT ct.name SEPARATOR ', ') as contact_names,
        GROUP_CONCAT(DISTINCT d.name SEPARATOR ', ') as deal_names
      FROM companies as c
      LEFT JOIN contacts as ct ON c.company_id = ct.company_id
      LEFT JOIN deals as d ON c.company_id = d.company_id
      WHERE 1=1 {$whereClause}
      GROUP BY c.company_id
      ORDER BY c.name ASC
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

  public function getTotalInstansi($params = [])
  {
    $bindings = [];
    $whereClause = $this->buildWhereClause($params, $bindings);
    $this->db->query("SELECT COUNT(DISTINCT c.company_id) as total FROM companies c WHERE 1=1 {$whereClause}");
    foreach ($bindings as $key => &$val) {
      $this->db->bind($key, $val);
    }
    $result = $this->db->single();
    return $result ? $result->total : 0;
  }

  public function getDistinctIndustries()
  {
    $this->db->query("SELECT DISTINCT industry FROM companies WHERE industry IS NOT NULL AND industry != '' ORDER BY industry ASC");
    return $this->db->resultSet();
  }

  public function getInstansiById($id)
  {
    $this->db->query('SELECT * FROM companies WHERE company_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->single();
  }

  public function getInstansiByName($name)
  {
    $this->db->query('SELECT * FROM companies WHERE name = :name');
    $this->db->bind(':name', $name);
    return $this->db->single();
  }

  public function addInstansi($data)
  {
    $this->db->query('INSERT INTO companies (name, website, industry, description, gmaps_location) VALUES (:name, :website, :industry, :description, :gmaps_location)');
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':website', $data['website']);
    $this->db->bind(':industry', $data['industry']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':gmaps_location', $data['gmaps_location']);

    if ($this->db->execute()) {
      return $this->db->lastInsertId();
    }
    return false;
  }

  public function updateInstansi($data)
  {
    $this->db->query('UPDATE companies SET name = :name, website = :website, industry = :industry, description = :description, gmaps_location = :gmaps_location WHERE company_id = :id');
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':website', $data['website']);
    $this->db->bind(':industry', $data['industry']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':gmaps_location', $data['gmaps_location']);
    return $this->db->execute();
  }

  public function deleteInstansi($id)
  {
    $this->db->query('DELETE FROM companies WHERE company_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }

  public function getContactsByInstansiId($company_id)
  {
    $this->db->query('SELECT * FROM contacts WHERE company_id = :company_id ORDER BY name ASC');
    $this->db->bind(':company_id', $company_id);
    return $this->db->resultSet();
  }

  public function getDealsByInstansiId($id)
  {
    $this->db->query('SELECT * FROM deals WHERE company_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->resultSet();
  }
}
