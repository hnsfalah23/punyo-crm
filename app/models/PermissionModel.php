<?php
// app/models/PermissionModel.php

class PermissionModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  public function getAllMenus()
  {
    $this->db->query('SELECT * FROM menus ORDER BY menu_order ASC');
    return $this->db->resultSet();
  }

  public function getPermissionsByRoleId($role_id)
  {
    $this->db->query('SELECT * FROM role_permissions WHERE role_id = :role_id');
    $this->db->bind(':role_id', $role_id);
    $results = $this->db->resultSet();
    // Kembalikan array asosiatif dengan menu_id sebagai key
    return array_column($results, null, 'menu_id');
  }

  public function updatePermissions($role_id, $permissions)
  {
    $this->db->query('DELETE FROM role_permissions WHERE role_id = :role_id');
    $this->db->bind(':role_id', $role_id);
    $this->db->execute();

    if (!empty($permissions)) {
      foreach ($permissions as $menu_id => $perms) {
        // **PERBAIKAN LOGIKA DI SINI**
        // Pastikan can_read selalu 1 jika ada izin lain yang dicentang
        $can_read = (isset($perms['create']) || isset($perms['update']) || isset($perms['delete']) || isset($perms['read'])) ? 1 : 0;

        $this->db->query('INSERT INTO role_permissions (role_id, menu_id, can_read, can_create, can_update, can_delete) VALUES (:role_id, :menu_id, :can_read, :can_create, :can_update, :can_delete)');
        $this->db->bind(':role_id', $role_id);
        $this->db->bind(':menu_id', $menu_id);
        $this->db->bind(':can_read', $can_read);
        $this->db->bind(':can_create', isset($perms['create']) ? 1 : 0);
        $this->db->bind(':can_update', isset($perms['update']) ? 1 : 0);
        $this->db->bind(':can_delete', isset($perms['delete']) ? 1 : 0);
        $this->db->execute();
      }
    }
    return true;
  }
}
