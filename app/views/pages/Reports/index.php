<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Laporan</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item active">Laporan</li>
    </ol>
  </div>

  <div class="row">
    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><i class="bi bi-people-fill me-2 text-primary"></i>Kinerja Tim</h5>
          <p class="card-text text-muted flex-grow-1">Lihat jumlah kesepakatan yang dimenangkan dan total pendapatan per staf.</p>
          <a href="<?= BASE_URL; ?>/reports/team_performance" class="btn btn-outline-primary mt-auto">Buka Laporan</a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><i class="bi bi-bullseye me-2 text-success"></i>Konversi Prospek</h5>
          <p class="card-text text-muted flex-grow-1">Analisis rasio keberhasilan dalam mengubah prospek menjadi kesepakatan.</p>
          <a href="<?= BASE_URL; ?>/reports/lead_conversion" class="btn btn-outline-success mt-auto">Buka Laporan</a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><i class="bi bi-box-seam-fill me-2 text-warning"></i>Penjualan per Produk</h5>
          <p class="card-text text-muted flex-grow-1">Lihat produk mana yang paling banyak terjual dan menghasilkan pendapatan terbanyak.</p>
          <a href="<?= BASE_URL; ?>/reports/sales_by_product" class="btn btn-outline-warning mt-auto">Buka Laporan</a>
        </div>
      </div>
    </div>
  </div>
</div>