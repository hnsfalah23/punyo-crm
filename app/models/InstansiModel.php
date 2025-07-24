<?php
// app/models/InstansiModel.php

class InstansiModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  // == Instansi ==
  public function getAllInstansi($scope_ids = [])
  {
    $sql = 'SELECT * FROM companies';
    if (!empty($scope_ids)) {
      // Pilih instansi yang memiliki kesepakatan yang dimiliki oleh user dalam scope
      $sql .= ' WHERE company_id IN (SELECT DISTINCT company_id FROM deals WHERE owner_id IN (' . implode(',', $scope_ids) . '))';
    }
    $sql .= ' ORDER BY name ASC';
    $this->db->query($sql);
    return $this->db->resultSet();
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

  public function getInstansiById($id)
  {
    $this->db->query('SELECT * FROM companies WHERE company_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->single();
  }

  public function getInstansiByName($name)
  {
    $this->db->query('SELECT * FROM companies WHERE name = :name LIMIT 1');
    $this->db->bind(':name', $name);
    $result = $this->db->single();
    return $result ? $result : false;
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
    // Diperbarui: Hapus kesepakatan terkait terlebih dahulu untuk menghindari error constraint
    // 1. Hapus semua kesepakatan (deals) yang terkait dengan instansi ini.
    $this->db->query('DELETE FROM deals WHERE company_id = :company_id');
    $this->db->bind(':company_id', $id);
    $this->db->execute();

    // 2. Setelah kesepakatan dihapus, baru aman untuk menghapus instansi.
    // ON DELETE CASCADE akan secara otomatis menghapus kontak yang terkait.
    $this->db->query('DELETE FROM companies WHERE company_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }

  // == Kontak ==
  public function getContactsByInstansiId($id)
  {
    $this->db->query('SELECT * FROM contacts WHERE company_id = :id ORDER BY name ASC');
    $this->db->bind(':id', $id);
    return $this->db->resultSet();
  }

  public function getAllContactsWithCompanyName()
  {
    $this->db->query('
            SELECT ct.*, co.name as company_name
            FROM contacts as ct
            JOIN companies as co ON ct.company_id = co.company_id
            ORDER BY ct.name ASC
        ');
    return $this->db->resultSet();
  }

  public function addContact($data)
  {
    $this->db->query('INSERT INTO contacts (name, contact_type, priority, email, job_title, phone, company_id) VALUES (:name, :contact_type, :priority, :email, :job_title, :phone, :company_id)');
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':contact_type', $data['contact_type']);
    $this->db->bind(':priority', $data['priority']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':job_title', $data['job_title']);
    $this->db->bind(':phone', $data['phone']);
    $this->db->bind(':company_id', $data['company_id']);
    if ($this->db->execute()) {
      return $this->db->lastInsertId();
    }
    return false;
  }

  public function getContactById($id)
  {
    $this->db->query('SELECT * FROM contacts WHERE contact_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->single();
  }

  public function updateContact($data)
  {
    $this->db->query('UPDATE contacts SET name = :name, contact_type = :contact_type, priority = :priority, email = :email, job_title = :job_title, phone = :phone WHERE contact_id = :id');
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':contact_type', $data['contact_type']);
    $this->db->bind(':priority', $data['priority']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':job_title', $data['job_title']);
    $this->db->bind(':phone', $data['phone']);
    return $this->db->execute();
  }

  public function deleteContact($id)
  {
    // Diperbarui: Hapus kesepakatan (deals) yang terkait dengan kontak ini terlebih dahulu.
    $this->db->query('DELETE FROM deals WHERE contact_id = :contact_id');
    $this->db->bind(':contact_id', $id);
    $this->db->execute();

    // Setelah itu, baru aman untuk menghapus kontak.
    $this->db->query('DELETE FROM contacts WHERE contact_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }


  public function getDealsByInstansiId($id)
  {
    $this->db->query('
            SELECT 
                d.*, 
                u.name as owner_name,
                GROUP_CONCAT(p.name SEPARATOR ", ") as product_names
            FROM deals as d
            JOIN users as u ON d.owner_id = u.user_id
            LEFT JOIN deal_products as dp ON d.deal_id = dp.deal_id
            LEFT JOIN products as p ON dp.product_id = p.product_id
            WHERE d.company_id = :id
            GROUP BY d.deal_id
            ORDER BY d.created_at DESC
        ');
    $this->db->bind(':id', $id);
    return $this->db->resultSet();
  }
}
