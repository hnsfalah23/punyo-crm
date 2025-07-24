<?php
// app/models/TargetModel.php

class TargetModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  public function getTargets($month, $type)
  {
    $this->db->query('SELECT user_id, value FROM targets WHERE target_month = :month AND target_type = :type');
    $this->db->bind(':month', $month);
    $this->db->bind(':type', $type);
    $results = $this->db->resultSet();
    // Kembalikan array dalam format [user_id => value]
    return array_column($results, 'value', 'user_id');
  }

  public function setOrUpdateTargets($targets, $month, $type)
  {
    foreach ($targets as $user_id => $value) {
      $valueToSave = !empty($value) ? $value : 0;

      $this->db->query('
                INSERT INTO targets (user_id, target_type, target_month, value) 
                VALUES (:user_id, :target_type, :target_month, :value)
                ON DUPLICATE KEY UPDATE value = :value_update
            ');
      $this->db->bind(':user_id', $user_id);
      $this->db->bind(':target_type', $type);
      $this->db->bind(':target_month', $month);
      $this->db->bind(':value', $valueToSave);
      $this->db->bind(':value_update', $valueToSave);
      $this->db->execute();
    }
    return true;
  }
}
