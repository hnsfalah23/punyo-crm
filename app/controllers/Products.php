<?php
// app/controllers/Products.php

class Products extends Controller
{
  private $productModel;

  public function __construct()
  {
    // Pengecekan baru yang dinamis
    if (!isLoggedIn()) {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }
    if (!can('read', 'products')) { // Cek izin BACA
      flash('dashboard_message', 'Anda tidak memiliki hak akses.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }
    $this->productModel = $this->model('Product');
  }

  public function index()
  {
    if (!can('read', 'products')) {
      flash('product_message', 'Anda tidak memiliki izin untuk melihat produk.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/dashboard');
      exit;
    }
    $data = [
      'title' => 'Manajemen Produk',
      'products' => $this->productModel->getAllProductsWithCategory(),
      'categories' => $this->productModel->getAllCategories()
    ];
    $this->renderView('pages/products/index', $data);
  }

  public function add()
  {
    if (!can('create', 'products')) {
      flash('product_message', 'Anda tidak memiliki izin untuk menambah produk.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/products');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $data = [
        'name' => trim($_POST['name']),
        'description' => trim($_POST['description']),
        'price' => trim($_POST['price']),
        'category_id' => $_POST['category_id'],
        'categories' => $this->productModel->getAllCategories(),
        'name_err' => '',
        'price_err' => '',
        'category_id_err' => ''
      ];

      if (empty($data['name'])) $data['name_err'] = 'Nama produk tidak boleh kosong.';
      if (empty($data['price'])) $data['price_err'] = 'Harga tidak boleh kosong.';
      elseif (!is_numeric($data['price'])) $data['price_err'] = 'Harga harus berupa angka.';
      if (empty($data['category_id'])) $data['category_id_err'] = 'Kategori harus dipilih.';

      if (empty($data['name_err']) && empty($data['price_err']) && empty($data['category_id_err'])) {
        if ($this->productModel->addProduct($data)) {
          flash('product_message', 'Produk baru berhasil ditambahkan.');
          header('Location: ' . BASE_URL . '/products');
          exit;
        } else {
          die('Terjadi kesalahan.');
        }
      } else {
        $data['title'] = 'Tambah Produk';
        $this->renderView('pages/products/add', $data);
      }
    } else {
      $data = [
        'title' => 'Tambah Produk',
        'name' => '',
        'description' => '',
        'price' => '',
        'category_id' => '',
        'categories' => $this->productModel->getAllCategories(),
        'name_err' => '',
        'price_err' => '',
        'category_id_err' => ''
      ];
      $this->renderView('pages/products/add', $data);
    }
  }

  public function edit($id)
  {
    if (!can('update', 'products')) {
      flash('product_message', 'Anda tidak memiliki izin untuk mengedit produk.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/products');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $data = [
        'id' => $id,
        'name' => trim($_POST['name']),
        'description' => trim($_POST['description']),
        'price' => trim($_POST['price']),
        'category_id' => $_POST['category_id'],
        'categories' => $this->productModel->getAllCategories(),
        'name_err' => '',
        'price_err' => '',
        'category_id_err' => ''
      ];

      if (empty($data['name'])) $data['name_err'] = 'Nama produk tidak boleh kosong.';
      if (empty($data['price'])) $data['price_err'] = 'Harga tidak boleh kosong.';
      elseif (!is_numeric($data['price'])) $data['price_err'] = 'Harga harus berupa angka.';
      if (empty($data['category_id'])) $data['category_id_err'] = 'Kategori harus dipilih.';

      if (empty($data['name_err']) && empty($data['price_err']) && empty($data['category_id_err'])) {
        if ($this->productModel->updateProduct($data)) {
          flash('product_message', 'Data produk berhasil diupdate.');
          header('Location: ' . BASE_URL . '/products');
          exit;
        } else {
          die('Terjadi kesalahan saat mengupdate produk.');
        }
      } else {
        $data['title'] = 'Edit Produk';
        $this->renderView('pages/products/edit', $data);
      }
    } else {
      $product = $this->productModel->getProductById($id);
      if (!$product) {
        header('Location: ' . BASE_URL . '/products');
        exit;
      }
      $data = [
        'title' => 'Edit Produk',
        'id' => $id,
        'name' => $product->name,
        'description' => $product->description,
        'price' => $product->price,
        'category_id' => $product->category_id,
        'categories' => $this->productModel->getAllCategories(),
        'name_err' => '',
        'price_err' => '',
        'category_id_err' => ''
      ];
      $this->renderView('pages/products/edit', $data);
    }
  }

  public function delete($id)
  {
    if (!can('delete', 'products')) {
      flash('product_message', 'Anda tidak memiliki izin untuk menghapus produk.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/products');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($this->productModel->deleteProduct($id)) {
        flash('product_message', 'Produk berhasil dihapus.');
        header('Location: ' . BASE_URL . '/products');
        exit;
      } else {
        die('Terjadi kesalahan saat menghapus produk.');
      }
    } else {
      header('Location: ' . BASE_URL . '/products');
      exit;
    }
  }

  public function addCategory()
  {
    if (!can('create', 'products')) {
      flash('product_message', 'Anda tidak memiliki izin untuk menambah kategori.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/products');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $categoryName = trim($_POST['category_name']);
      if (!empty($categoryName)) {
        if ($this->productModel->addCategory($categoryName)) {
          flash('product_message', 'Kategori baru berhasil ditambahkan.');
        } else {
          flash('product_message', 'Gagal menambahkan kategori.', 'error');
        }
      } else {
        flash('product_message', 'Nama kategori tidak boleh kosong.', 'error');
      }
    }
    header('Location: ' . BASE_URL . '/products');
    exit;
  }

  public function deleteCategory($id)
  {
    if (!can('delete', 'products')) {
      flash('product_message', 'Anda tidak memiliki izin untuk menghapus kategori.', 'alert alert-danger');
      header('Location: ' . BASE_URL . '/products');
      exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($this->productModel->deleteCategory($id)) {
        flash('product_message', 'Kategori berhasil dihapus.');
      } else {
        flash('product_message', 'Gagal menghapus kategori. Pastikan tidak ada produk yang menggunakan kategori ini.', 'error');
      }
    }
    header('Location: ' . BASE_URL . '/products');
    exit;
  }

  private function renderView($view, $data = [])
  {
    $this->view('layouts/header', $data);
    $this->view('layouts/sidebar', $data);
    echo '<div id="page-content-wrapper">';
    $this->view('layouts/topbar', $data);
    $this->view($view, $data);
    echo '</div>';
    $this->view('layouts/footer', $data);
  }
}
