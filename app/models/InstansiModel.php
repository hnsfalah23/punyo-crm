<?php
// app/models/InstansiModel.php

class InstansiModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  /**
   * Helper untuk membangun klausa WHERE secara dinamis untuk pencarian dan filter.
   */
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

  /**
   * Mengambil daftar instansi dengan paginasi, pencarian, dan filter.
   */
  public function getInstansi($params = [])
  {
    $bindings = [];
    $whereClause = $this->buildWhereClause($params, $bindings);

    $sql = "
            SELECT 
                c.company_id, c.name, c.website, c.industry, c.description, c.gmaps_location,
                (SELECT COUNT(contact_id) FROM contacts WHERE company_id = c.company_id) as total_contacts,
                (SELECT COUNT(deal_id) FROM deals WHERE company_id = c.company_id) as total_deals
            FROM companies as c
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

  /**
   * Menghitung total instansi untuk paginasi berdasarkan filter.
   */
  public function getTotalInstansi($params = [])
  {
    $bindings = [];
    $whereClause = $this->buildWhereClause($params, $bindings);
    $this->db->query("SELECT COUNT(DISTINCT c.company_id) as total FROM companies c WHERE 1=1 {$whereClause}");
    foreach ($bindings as $key => &$val) {
      $this->db->bind($key, $val);
    }
    $result = $this->db->single();
    return $result ? (int) $result->total : 0;
  }

  /**
   * Mengambil daftar industri yang unik untuk dropdown filter.
   */
  public function getDistinctIndustries()
  {
    $this->db->query("SELECT DISTINCT industry FROM companies WHERE industry IS NOT NULL AND industry != '' ORDER BY industry ASC");
    return $this->db->resultSet();
  }

  /**
   * Mengambil satu data instansi berdasarkan ID.
   */
  public function getInstansiById($id)
  {
    $this->db->query('SELECT * FROM companies WHERE company_id = :id');
    $this->db->bind(':id', $id, PDO::PARAM_INT);
    return $this->db->single();
  }

  /**
   * Menambahkan instansi baru ke database.
   */
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

  /**
   * Memperbarui data instansi di database.
   */
  public function updateInstansi($data)
  {
    $this->db->query('UPDATE companies SET name = :name, website = :website, industry = :industry, description = :description, gmaps_location = :gmaps_location WHERE company_id = :id');
    $this->db->bind(':id', $data['id'], PDO::PARAM_INT);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':website', $data['website']);
    $this->db->bind(':industry', $data['industry']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':gmaps_location', $data['gmaps_location']);
    return $this->db->execute();
  }

  /**
   * Menghapus instansi dari database.
   */
  public function deleteInstansi($id)
  {
    if ($this->hasRelatedData($id)) {
      return false;
    }
    $this->db->query('DELETE FROM companies WHERE company_id = :id');
    $this->db->bind(':id', $id, PDO::PARAM_INT);
    return $this->db->execute();
  }

  /**
   * Memeriksa apakah sebuah instansi memiliki data terkait.
   */
  public function hasRelatedData($company_id)
  {
    $this->db->query('SELECT (
            (SELECT COUNT(*) FROM contacts WHERE company_id = :id1) + 
            (SELECT COUNT(*) FROM deals WHERE company_id = :id2)
        ) as total');
    $this->db->bind(':id1', $company_id, PDO::PARAM_INT);
    $this->db->bind(':id2', $company_id, PDO::PARAM_INT);
    $result = $this->db->single();
    return $result && $result->total > 0;
  }

  public function getContactsByInstansiId($company_id)
  {
    $this->db->query('SELECT * FROM contacts WHERE company_id = :company_id ORDER BY name ASC');
    $this->db->bind(':company_id', $company_id, PDO::PARAM_INT);
    return $this->db->resultSet();
  }

  public function getDealsByInstansiId($id)
  {
    $this->db->query('SELECT * FROM deals WHERE company_id = :id');
    $this->db->bind(':id', $id, PDO::PARAM_INT);
    return $this->db->resultSet();
  }

  /**
   * FUNGSI BARU: Mengambil produk terkait berdasarkan ID instansi.
   *
   * @param int $company_id ID Instansi.
   * @return array Daftar produk.
   */
  public function getProductsByInstansiId($company_id)
  {
    // Asumsi ada tabel 'products' dengan kolom 'company_id'
    $this->db->query('SELECT * FROM products WHERE company_id = :company_id ORDER BY name ASC');
    $this->db->bind(':company_id', $company_id, PDO::PARAM_INT);
    return $this->db->resultSet();
  }

  public function getInstansiByName($name)
  {
    $this->db->query('SELECT * FROM companies WHERE name = :name');
    $this->db->bind(':name', $name);
    return $this->db->single();
  }
}
