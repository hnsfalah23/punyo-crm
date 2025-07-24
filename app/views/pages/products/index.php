<style>
  .table-hover tbody tr {
    transition: all 0.2s ease-in-out;
  }

  .table-hover tbody tr:hover {
    background-color: #f8f9fa;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
    z-index: 2;
    position: relative;
  }

  .action-btn {
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    text-decoration: none;
    margin: 0 2px;
    transition: all 0.2s ease;
  }

  .action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  }
</style>

<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Manajemen Produk</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item active">Produk</li>
    </ol>
  </div>

  <?php flash('product_message'); ?>

  <div class="row">
    <div class="col-lg-4">
      <div class="card mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header"><i class="bi bi-tags-fill me-1"></i> Kategori Produk</div>
        <div class="card-body">
          <ul class="list-group list-group-flush mb-3">
            <?php foreach ($data['categories'] as $category): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($category->category_name); ?>
                <?php if (can('delete', 'products')): ?>
                  <form action="<?= BASE_URL; ?>/products/deleteCategory/<?= $category->category_id; ?>" method="post" class="d-inline form-delete" data-item-name="<?= htmlspecialchars($category->category_name); ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Hapus Kategori"><i class="bi bi-trash"></i></button>
                  </form>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
          <?php if (can('create', 'products')): ?>
            <hr>
            <form action="<?= BASE_URL; ?>/products/addCategory" method="POST">
              <label class="form-label small">Tambah Kategori Baru</label>
              <div class="input-group">
                <input type="text" name="category_name" class="form-control" placeholder="Nama Kategori" required>
                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-plus"></i></button>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-box-seam-fill me-1"></i> Data Produk</span>
          <?php if (can('create', 'products')): ?>
            <a href="<?= BASE_URL; ?>/products/add" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Produk</a>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Nama Produk</th>
                  <th>Kategori</th>
                  <th>Harga (Rp)</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($data['products'])): ?>
                  <tr>
                    <td colspan="4" class="text-center py-5">Belum ada produk.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($data['products'] as $product) : ?>
                    <tr>
                      <td><strong><?= htmlspecialchars($product->name); ?></strong></td>
                      <td><span class="badge bg-secondary fw-normal"><?= htmlspecialchars($product->category_name); ?></span></td>
                      <td><?= number_format($product->price, 0, ',', '.'); ?></td>
                      <td class="text-center">
                        <?php if (can('update', 'products')): ?>
                          <a href="<?= BASE_URL; ?>/products/edit/<?= $product->product_id; ?>" class="btn btn-warning btn-sm text-white action-btn" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                        <?php endif; ?>
                        <?php if (can('delete', 'products')): ?>
                          <form action="<?= BASE_URL; ?>/products/delete/<?= $product->product_id; ?>" method="post" class="d-inline form-delete" data-item-name="<?= htmlspecialchars($product->name); ?>">
                            <button type="submit" class="btn btn-danger btn-sm action-btn" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                          </form>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>