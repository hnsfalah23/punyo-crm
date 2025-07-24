<div class="container-fluid px-4">
  <h1 class="mt-4"><?= $data['title']; ?></h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/reports">Laporan</a></li>
    <li class="breadcrumb-item active">Konversi Prospek</li>
  </ol>
  <div class="card mb-4">
    <div class="card-header"><i class="bi bi-table me-1"></i>Hasil Laporan</div>
    <div class="card-body">
      <?php
      $terkualifikasi = $data['conversion_data']['Terkualifikasi'];
      $gagal = $data['conversion_data']['Gagal'];
      $total = $terkualifikasi + $gagal;
      $rate = ($total > 0) ? round(($terkualifikasi / $total) * 100) : 0;
      ?>
      <div class="row text-center">
        <div class="col-md-4">
          <div class="p-3 border rounded">
            <h3 class="display-6 text-success"><?= $terkualifikasi; ?></h3>
            <p class="text-muted mb-0">Prospek Terkualifikasi</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 border rounded">
            <h3 class="display-6 text-danger"><?= $gagal; ?></h3>
            <p class="text-muted mb-0">Prospek Gagal</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 border rounded bg-light">
            <h3 class="display-6 text-primary"><?= $rate; ?>%</h3>
            <p class="text-muted mb-0">Tingkat Konversi</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>