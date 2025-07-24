<div class="container-fluid px-4">
  <h1 class="mt-4"><?= $data['title']; ?></h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/reports">Laporan</a></li>
    <li class="breadcrumb-item active">Penjualan per Produk</li>
  </ol>
  <div class="card mb-4">
    <div class="card-header"><i class="bi bi-table me-1"></i>Hasil Laporan</div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th>Nama Produk</th>
              <th>Total Unit Terjual</th>
              <th>Total Pendapatan (Rp)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data['sales_data'] as $row) : ?>
              <tr>
                <td><?= htmlspecialchars($row->product_name); ?></td>
                <td><?= $row->total_quantity; ?></td>
                <td><?= number_format($row->total_revenue, 0, ',', '.'); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>