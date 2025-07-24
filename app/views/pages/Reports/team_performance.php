<!-- app/views/pages/reports/team_performance.php -->
<div class="container-fluid px-4">
  <h1 class="mt-4"><?= $data['title']; ?></h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/reports">Laporan</a></li>
    <li class="breadcrumb-item active">Kinerja Tim</li>
  </ol>

  <div class="card mb-4">
    <div class="card-header">
      <i class="bi bi-filter me-1"></i>
      Filter Laporan
    </div>
    <div class="card-body">
      <form action="<?= BASE_URL; ?>/reports/team_performance" method="POST">
        <div class="row align-items-end">
          <div class="col-md-4">
            <label for="start_date" class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control" value="<?= $data['start_date']; ?>">
          </div>
          <div class="col-md-4">
            <label for="end_date" class="form-label">Tanggal Selesai</label>
            <input type="date" name="end_date" class="form-control" value="<?= $data['end_date']; ?>">
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Terapkan</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <i class="bi bi-table me-1"></i>
      Hasil Laporan untuk periode <?= date('d M Y', strtotime($data['start_date'])); ?> - <?= date('d M Y', strtotime($data['end_date'])); ?>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th>Nama Staf</th>
              <th>Jumlah Deal Menang</th>
              <th>Total Pendapatan (Rp)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($data['performance_data'])): ?>
              <tr>
                <td colspan="3" class="text-center">Tidak ada data untuk periode yang dipilih.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($data['performance_data'] as $row) : ?>
                <tr>
                  <td><?= htmlspecialchars($row->user_name); ?></td>
                  <td><?= $row->total_deals_won; ?></td>
                  <td><?= number_format($row->total_revenue, 0, ',', '.'); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>