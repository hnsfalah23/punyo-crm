<?php
// app/models/KontakModel.php

class KontakModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  /**
   * Mengambil satu data kontak berdasarkan ID.
   */
  public function getKontakById($id)
  {
    $this->db->query('SELECT * FROM contacts WHERE contact_id = :id');
    $this->db->bind(':id', $id, PDO::PARAM_INT);
    return $this->db->single();
  }

  /**
   * FUNGSI BARU: Mengambil kontak terakhir yang ditambahkan untuk sebuah instansi.
   * @param int $company_id ID Instansi.
   * @return object|false Data kontak terakhir atau false jika tidak ditemukan.
   */
  public function getLastContactByCompanyId($company_id)
  {
    $this->db->query('SELECT * FROM contacts WHERE company_id = :company_id ORDER BY contact_id DESC LIMIT 1');
    $this->db->bind(':company_id', $company_id, PDO::PARAM_INT);
    return $this->db->single();
  }

  /**
   * Menambahkan kontak baru ke database.
   */
  public function addKontak($data)
  {
    $this->db->query('INSERT INTO contacts (name, email, phone, company_id) VALUES (:name, :email, :phone, :company_id)');

    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':phone', $data['phone']);
    $this->db->bind(':company_id', $data['company_id'], PDO::PARAM_INT);

    return $this->db->execute();
  }

  /**
   * Memperbarui data kontak di database.
   */
  public function updateKontak($data)
  {
    $this->db->query('UPDATE contacts SET name = :name, email = :email, phone = :phone WHERE contact_id = :contact_id');

    $this->db->bind(':contact_id', $data['contact_id'], PDO::PARAM_INT);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':phone', $data['phone']);

    return $this->db->execute();
  }

  /**
   * Menghapus kontak dari database.
   */
  public function deleteKontak($id)
  {
    $this->db->query('DELETE FROM contacts WHERE contact_id = :id');
    $this->db->bind(':id', $id, PDO::PARAM_INT);
    return $this->db->execute();
  }
}
