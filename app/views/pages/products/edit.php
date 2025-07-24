<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Edit Produk</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/products">Manajemen Produk</a></li>
      <li class="breadcrumb-item active">Edit</li>
    </ol>
  </div>

  <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
      <i class="bi bi-pencil-square me-1"></i>
      Formulir Edit Produk
    </div>
    <div class="card-body">
      <form action="<?= BASE_URL; ?>/products/edit/<?= $data['id']; ?>" method="POST">
        <div class="form-floating mb-3">
          <input type="text" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" id="name" name="name" placeholder="Nama Produk" value="<?= $data['name']; ?>">
          <label for="name">Nama Produk</label>
          <span class="invalid-feedback"><?= $data['name_err']; ?></span>
        </div>
        <div class="form-floating mb-3">
          <select name="category_id" id="category_id" class="form-select <?= (!empty($data['category_id_err'])) ? 'is-invalid' : ''; ?>">
            <option value="">Pilih Kategori...</option>
            <?php foreach ($data['categories'] as $category): ?>
              <option value="<?= $category->category_id; ?>" <?= ($data['category_id'] == $category->category_id) ? 'selected' : ''; ?>><?= $category->category_name; ?></option>
            <?php endforeach; ?>
          </select>
          <label for="category_id">Kategori</label>
          <span class="invalid-feedback"><?= $data['category_id_err']; ?></span>
        </div>
        <div class="form-floating mb-3">
          <input type="number" class="form-control <?= (!empty($data['price_err'])) ? 'is-invalid' : ''; ?>" id="price" name="price" placeholder="Harga" value="<?= $data['price']; ?>">
          <label for="price">Harga (Rp)</label>
          <span class="invalid-feedback"><?= $data['price_err']; ?></span>
        </div>
        <div class="form-floating mb-3">
          <textarea class="form-control" placeholder="Deskripsi" id="description" name="description" style="height: 100px"><?= $data['description']; ?></textarea>
          <label for="description">Deskripsi</label>
        </div>

        <div class="d-flex justify-content-end mt-3">
          <a href="<?= BASE_URL; ?>/products" class="btn btn-secondary me-2">Batal</a>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Update Produk</button>
        </div>
      </form>
    </div>
  </div>
</div>