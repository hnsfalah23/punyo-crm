<?php
// app/models/ActivityModel.php

class ActivityModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  private function handleFileUpload($file)
  {
    if (isset($file) && $file['error'] == 0) {
      $targetDir = PUBLIC_ROOT . "/uploads/activities/";
      if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
      }
      $fileName = uniqid() . '_' . basename($file["name"]);
      $targetFile = $targetDir . $fileName;
      if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $fileName;
      }
    }
    return null;
  }

  public function getActivitiesByItemId($item_id, $item_type)
  {
    $this->db->query('
            SELECT a.*, u.name as owner_name
            FROM activities as a
            JOIN users as u ON a.owner_id = u.user_id
            WHERE a.related_item_id = :item_id AND a.related_item_type = :item_type
            ORDER BY a.start_time DESC
        ');
    $this->db->bind(':item_id', $item_id);
    $this->db->bind(':item_type', $item_type);
    return $this->db->resultSet();
  }

  public function getActivityById($id)
  {
    $this->db->query('SELECT * FROM activities WHERE activity_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->single();
  }

  public function getAllActivitiesDetails($params = [])
  {
    $bindings = [];
    $whereClause = '';

    if (isset($params['scope_type'])) {
      if ($params['scope_type'] == 'division') {
        $whereClause .= ' AND u.division_id = :division_id';
        $bindings[':division_id'] = $params['scope_value'];
      } elseif ($params['scope_type'] == 'self') {
        $whereClause .= ' AND a.owner_id = :owner_id';
        $bindings[':owner_id'] = $params['scope_value'];
      }
    }

    $this->db->query("
            SELECT 
                a.*,
                u.name as owner_name,
                CASE 
                    WHEN a.related_item_type = 'lead' THEN l.name
                    WHEN a.related_item_type = 'deal' THEN d.name
                    ELSE 'N/A'
                END as related_item_name,
                CASE 
                    WHEN a.related_item_type = 'lead' THEN CONCAT('prospek/detail/', a.related_item_id)
                    WHEN a.related_item_type = 'deal' THEN CONCAT('peluang/detail/', a.related_item_id)
                    ELSE '#'
                END as related_item_link
            FROM activities a
            JOIN users u ON a.owner_id = u.user_id
            LEFT JOIN leads l ON a.related_item_id = l.lead_id AND a.related_item_type = 'lead'
            LEFT JOIN deals d ON a.related_item_id = d.deal_id AND a.related_item_type = 'deal'
            WHERE 1=1 {$whereClause}
            ORDER BY a.start_time DESC
        ");

    foreach ($bindings as $key => $val) {
      $this->db->bind($key, $val);
    }

    return $this->db->resultSet();
  }

  public function addActivity($data)
  {
    $photoName = $this->handleFileUpload($data['documentation_photo']);

    $this->db->query('INSERT INTO activities (name, type, description, start_time, end_time, owner_id, related_item_id, related_item_type, documentation_photo) VALUES (:name, :type, :description, :start_time, :end_time, :owner_id, :related_item_id, :related_item_type, :documentation_photo)');

    $this->db->bind(':name', $data['name']);
    $this->db->bind(':type', $data['type']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':start_time', $data['start_time']);
    $this->db->bind(':end_time', $data['end_time']);
    $this->db->bind(':owner_id', $data['owner_id']);
    $this->db->bind(':related_item_id', $data['related_item_id']);
    $this->db->bind(':related_item_type', $data['related_item_type']);
    $this->db->bind(':documentation_photo', $photoName);

    return $this->db->execute();
  }

  public function updateActivity($data)
  {
    $activity = $this->getActivityById($data['id']);
    $photoName = $activity->documentation_photo;

    if (isset($data['documentation_photo']) && $data['documentation_photo']['error'] == 0) {
      if ($photoName && file_exists(PUBLIC_ROOT . '/uploads/activities/' . $photoName)) {
        unlink(PUBLIC_ROOT . '/uploads/activities/' . $photoName);
      }
      $photoName = $this->handleFileUpload($data['documentation_photo']);
    }

    $this->db->query('UPDATE activities SET name = :name, type = :type, description = :description, start_time = :start_time, end_time = :end_time, documentation_photo = :documentation_photo WHERE activity_id = :id');

    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':type', $data['type']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':start_time', $data['start_time']);
    $this->db->bind(':end_time', $data['end_time']);
    $this->db->bind(':documentation_photo', $photoName);

    return $this->db->execute();
  }

  public function deleteActivity($id)
  {
    $activity = $this->getActivityById($id);
    if ($activity && $activity->documentation_photo) {
      if (file_exists(PUBLIC_ROOT . '/uploads/activities/' . $activity->documentation_photo)) {
        unlink(PUBLIC_ROOT . '/uploads/activities/' . $activity->documentation_photo);
      }
    }
    $this->db->query('DELETE FROM activities WHERE activity_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }
}
