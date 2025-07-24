<?php
// app/models/Product.php

class Product
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  // == Kategori ==
  public function getAllCategories()
  {
    $this->db->query('SELECT * FROM product_categories ORDER BY category_name ASC');
    return $this->db->resultSet();
  }

  public function addCategory($name)
  {
    $this->db->query('INSERT INTO product_categories (category_name) VALUES (:name)');
    $this->db->bind(':name', $name);
    return $this->db->execute();
  }

  public function deleteCategory($id)
  {
    $this->db->query('DELETE FROM product_categories WHERE category_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }

  // == Produk ==
  public function getAllProductsWithCategory()
  {
    $this->db->query('
            SELECT p.*, c.category_name 
            FROM products as p
            JOIN product_categories as c ON p.category_id = c.category_id
            ORDER BY p.product_id DESC
        ');
    return $this->db->resultSet();
  }

  public function addProduct($data)
  {
    $this->db->query('INSERT INTO products (name, description, price, category_id) VALUES (:name, :description, :price, :category_id)');
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':price', $data['price']);
    $this->db->bind(':category_id', $data['category_id']);
    return $this->db->execute();
  }

  public function getProductById($id)
  {
    $this->db->query('SELECT * FROM products WHERE product_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->single();
  }

  public function updateProduct($data)
  {
    $this->db->query('UPDATE products SET name = :name, description = :description, price = :price, category_id = :category_id WHERE product_id = :id');
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':price', $data['price']);
    $this->db->bind(':category_id', $data['category_id']);
    return $this->db->execute();
  }

  public function deleteProduct($id)
  {
    $this->db->query('DELETE FROM products WHERE product_id = :id');
    $this->db->bind(':id', $id);
    return $this->db->execute();
  }

  public function getAllProducts()
  {
    $this->db->query('SELECT * FROM products ORDER BY name ASC');
    return $this->db->resultSet();
  }

  public function getProductsByCategoryId($category_id)
  {
    $this->db->query('SELECT product_id, name, price FROM products WHERE category_id = :category_id ORDER BY name ASC');
    $this->db->bind(':category_id', $category_id);
    return $this->db->resultSet();
  }
}
