<?php
// app/models/ActivityModel.php

class ActivityModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
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

  public function addActivity($data)
  {
    $this->db->query('INSERT INTO activities (name, type, description, start_time, end_time, owner_id, related_item_id, related_item_type, documentation_photo) VALUES (:name, :type, :description, :start_time, :end_time, :owner_id, :related_item_id, :related_item_type, :documentation_photo)');

    $this->db->bind(':name', $data['name']);
    $this->db->bind(':type', $data['type']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':start_time', $data['start_time']);
    $this->db->bind(':end_time', $data['end_time']);
    $this->db->bind(':owner_id', $data['owner_id']);
    $this->db->bind(':related_item_id', $data['related_item_id']);
    $this->db->bind(':related_item_type', $data['related_item_type']);
    $this->db->bind(':documentation_photo', $data['documentation_photo']);

    return $this->db->execute();
  }

  public function updateActivity($data)
  {
    $this->db->query('UPDATE activities SET name = :name, type = :type, description = :description, start_time = :start_time, end_time = :end_time, documentation_photo = :documentation_photo WHERE activity_id = :id');
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':type', $data['type']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':start_time', $data['start_time']);
    $this->db->bind(':end_time', $data['end_time']);
    $this->db->bind(':documentation_photo', $data['documentation_photo']);
    return $this->db->execute();
  }

  public function deleteActivity($id)
  {
    $this->db->query('DELETE FROM activities WHERE activity_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }
}
