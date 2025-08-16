<?php
// app/models/User.php

class User
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  public function login($email, $password)
  {
    $this->db->query('SELECT * FROM users WHERE email = :email');
    $this->db->bind(':email', $email);
    $row = $this->db->single();

    if ($row) {
      $hashed_password = $row->password;
      if (password_verify($password, $hashed_password)) {
        return $row;
      }
    }
    return false;
  }

  public function findUserByEmail($email)
  {
    $this->db->query('SELECT * FROM users WHERE email = :email');
    $this->db->bind(':email', $email);
    $row = $this->db->single();
    return ($this->db->rowCount() > 0) ? $row : false;
  }

  public function register($data)
  {
    // Query untuk memasukkan user baru
    $this->db->query('INSERT INTO users (name, email, password, role_id) VALUES (:name, :email, :password, :role_id)');

    // Bind values
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':password', $data['password']);
    // Atur role_id default untuk setiap user yang mendaftar
    $this->db->bind(':role_id', 3);

    // Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function getAllUsersWithRoles()
  {
    $this->db->query('
            SELECT users.*, roles.role_name 
            FROM users 
            JOIN roles ON users.role_id = roles.role_id
            ORDER BY users.user_id DESC
        ');
    return $this->db->resultSet();
  }

  public function getAllRoles()
  {
    $this->db->query('SELECT * FROM roles');
    return $this->db->resultSet();
  }

  public function addUser($data)
  {
    $this->db->query('INSERT INTO users (name, email, password, role_id) VALUES(:name, :email, :password, :role_id)');
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':password', $data['password']);
    $this->db->bind(':role_id', $data['role_id']);
    return $this->db->execute();
  }

  public function getUserById($id)
  {
    $this->db->query('SELECT * FROM users WHERE user_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->single();
  }

  public function updateUser($data)
  {
    if (!empty($data['password'])) {
      $this->db->query('UPDATE users SET name = :name, email = :email, password = :password, role_id = :role_id WHERE user_id = :id');
      $this->db->bind(':password', $data['password']);
    } else {
      $this->db->query('UPDATE users SET name = :name, email = :email, role_id = :role_id WHERE user_id = :id');
    }
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':role_id', $data['role_id']);
    return $this->db->execute();
  }

  public function deleteUser($id)
  {
    $this->db->query('DELETE FROM users WHERE user_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }

  public function updateProfile($data)
  {
    if (!empty($data['password'])) {
      $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
      $this->db->query('UPDATE users SET name = :name, email = :email, phone = :phone, profile_picture = :profile_picture, password = :password WHERE user_id = :id');
      $this->db->bind(':password', $hashed_password);
    } else {
      $this->db->query('UPDATE users SET name = :name, email = :email, phone = :phone, profile_picture = :profile_picture WHERE user_id = :id');
    }

    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':phone', $data['phone']);
    $this->db->bind(':profile_picture', $data['profile_picture']);

    return $this->db->execute();
  }

  public function getTargetableUsers()
  {
    $this->db->query("
            SELECT u.*, r.role_name FROM users as u
            JOIN roles as r ON u.role_id = r.role_id
            WHERE r.role_name IN ('Staf Marketing', 'SPV Marketing Retail', 'SPV Marketing Corporate')
            ORDER BY r.role_id, u.name ASC
        ");
    return $this->db->resultSet();
  }
}
