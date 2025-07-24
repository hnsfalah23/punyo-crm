<?php
// app/controllers/Api.php

class Api extends Controller
{
  private $productModel;

  public function __construct()
  {
    // Tidak perlu cek login untuk API ini karena hanya mengambil data publik produk
    $this->productModel = $this->model('Product');
  }

  public function getProductsByCategory($category_id)
  {
    header('Content-Type: application/json');
    $products = $this->productModel->getProductsByCategoryId($category_id);
    echo json_encode($products);
    exit;
  }
}
