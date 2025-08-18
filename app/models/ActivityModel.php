<?php
// app/models/ActivityModel.php

class ActivityModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  public function getActivitiesByItemId($itemId, $itemType)
  {
    $this->db->query('SELECT activities.*, users.name as owner_name 
                          FROM activities 
                          JOIN users ON activities.owner_id = users.user_id 
                          WHERE related_item_id = :item_id AND related_item_type = :item_type 
                          ORDER BY start_time DESC');
    $this->db->bind(':item_id', $itemId);
    $this->db->bind(':item_type', $itemType);
    return $this->db->resultSet();
  }

  // [FIXED] Mengganti 'activitas' menjadi 'activities'
  public function getAllActivities()
  {
    $this->db->query("
      SELECT 
        activities.*, 
        users.name as owner_name 
      FROM 
        activities 
      JOIN 
        users ON activities.owner_id = users.user_id 
      ORDER BY 
        activities.start_time DESC
    ");
    return $this->db->resultSet();
  }

  public function getAllActivitiesForCalendar($user_id = null)
  {
    $sql = "
      SELECT 
        activities.activity_id,
        activities.name,
        activities.type,
        activities.description,
        activities.start_time,
        activities.end_time,
        activities.related_item_type,
        activities.documentation_photo,
        users.name as owner_name 
      FROM 
        activities 
      JOIN 
        users ON activities.owner_id = users.user_id";

    if ($user_id !== null) {
      $sql .= " WHERE activities.owner_id = :user_id";
    }

    $sql .= " ORDER BY activities.start_time ASC";

    $this->db->query($sql);

    if ($user_id !== null) {
      $this->db->bind(':user_id', $user_id);
    }

    return $this->db->resultSet();
  }


  // METHOD BARU: Mengambil aktivitas mendatang
  public function getUpcomingActivities($user_id = null)
  {
    $sql = "
        SELECT a.*, u.name as owner_name 
        FROM activities a
        JOIN users u ON a.owner_id = u.user_id
        WHERE a.start_time >= NOW()";

    if ($user_id !== null) {
      $sql .= " AND a.owner_id = :user_id";
    }

    $sql .= " ORDER BY a.start_time ASC";

    $this->db->query($sql);

    if ($user_id !== null) {
      $this->db->bind(':user_id', $user_id);
    }

    return $this->db->resultSet();
  }

  // METHOD BARU: Mengambil aktivitas yang sudah berlalu
  public function getPastActivities($user_id = null)
  {
    $sql = "
        SELECT a.*, u.name as owner_name 
        FROM activities a
        JOIN users u ON a.owner_id = u.user_id
        WHERE a.start_time < NOW()";

    if ($user_id !== null) {
      $sql .= " AND a.owner_id = :user_id";
    }

    $sql .= " ORDER BY a.start_time DESC";

    $this->db->query($sql);

    if ($user_id !== null) {
      $this->db->bind(':user_id', $user_id);
    }

    return $this->db->resultSet();
  }

  // METHOD BARU: Mengambil aktivitas berdasarkan range tanggal untuk kalender
  public function getActivitiesByDateRange($start_date, $end_date)
  {
    $this->db->query("
      SELECT 
        activities.activity_id,
        activities.name,
        activities.type,
        activities.description,
        activities.start_time,
        activities.end_time,
        activities.related_item_type,
        activities.documentation_photo,
        users.name as owner_name 
      FROM 
        activities 
      JOIN 
        users ON activities.owner_id = users.user_id 
      WHERE 
        activities.start_time BETWEEN :start_date AND :end_date
      ORDER BY 
        activities.start_time ASC
    ");

    $this->db->bind(':start_date', $start_date);
    $this->db->bind(':end_date', $end_date);

    return $this->db->resultSet();
  }

  public function addActivity($data)
  {
    $this->db->query('INSERT INTO activities (name, type, description, start_time, end_time, owner_id, related_item_id, related_item_type, documentation_photo) VALUES (:name, :type, :description, :start_time, :end_time, :owner_id, :related_item_id, :related_item_type, :documentation_photo)');

    $this->db->bind(':name', $data['name']);
    $this->db->bind(':type', $data['type']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':start_time', $data['start_time']);
    $this->db->bind(':end_time', !empty($data['end_time']) ? $data['end_time'] : null);
    $this->db->bind(':owner_id', $_SESSION['user_id']);
    $this->db->bind(':related_item_id', $data['related_item_id']);
    $this->db->bind(':related_item_type', $data['related_item_type']);
    $this->db->bind(':documentation_photo', $data['documentation_photo']);

    return $this->db->execute();
  }

  public function getActivityById($id)
  {
    // [FIXED] Mengganti 'activitas' menjadi 'activities'
    $this->db->query('SELECT * FROM activities WHERE activity_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->single();
  }

  public function updateActivity($data)
  {
    // [IMPROVED] Menambahkan logika untuk menangani unggahan file foto baru
    $photoPath = $this->getActivityById($data['id'])->documentation_photo; // Ambil path foto lama
    if (isset($data['documentation_photo']) && $data['documentation_photo']['error'] == 0) {
      $photoPath = $this->handleFileUpload($data['documentation_photo']);
    }

    // [FIXED] Memperbaiki query UPDATE
    $this->db->query('
      UPDATE activities 
      SET name = :name, type = :type, description = :description, start_time = :start_time, end_time = :end_time, documentation_photo = :documentation_photo 
      WHERE activity_id = :id
    ');

    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':type', $data['type']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':start_time', $data['start_time']);
    $this->db->bind(':end_time', $data['end_time']);
    $this->db->bind(':documentation_photo', $photoPath);

    return $this->db->execute();
  }

  public function deleteActivity($id)
  {
    // [FIXED] Mengganti 'activitas' menjadi 'activities'
    $this->db->query('DELETE FROM activities WHERE activity_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }

  /**
   * Helper function untuk menangani unggahan file.
   * @return string|null Nama file yang disimpan, atau null jika gagal.
   */
  private function handleFileUpload($file)
  {
    if ($file['error'] !== UPLOAD_ERR_OK) {
      return null;
    }

    $uploadDir = 'uploads/activities/';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
      return $targetPath;
    }

    return null;
  }
}
