<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Konversi Prospek: <?= htmlspecialchars($data['lead']->name ?? ''); ?></h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/leads">Manajemen Prospek</a></li>
      <li class="breadcrumb-item active">Konversi</li>
    </ol>
  </div>

  <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
      <i class="bi bi-check2-circle me-1"></i>
      Formulir Konversi Prospek
    </div>
    <div class="card-body">
      <form action="<?= BASE_URL; ?>/leads/convert/<?= $data['lead']->lead_id; ?>" method="POST">
        <p class="text-muted">Buat Instansi, Kontak, dan Kesepakatan baru dari data Prospek ini. Anda dapat mengubah detailnya sebelum menyimpan.</p>
        <div class="row gx-4 mt-4">
          <!-- Kolom Kiri: Instansi & Kontak -->
          <div class="col-lg-6">
            <h5><i class="bi bi-building me-2"></i>Data Instansi Baru</h5>
            <hr>
            <div class="form-floating mb-4">
              <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Nama Instansi" value="<?= htmlspecialchars($data['lead']->company_name ?? ''); ?>" required>
              <label for="company_name">Nama Instansi</label>
            </div>

            <h5><i class="bi bi-person-badge me-2"></i>Data Kontak Baru</h5>
            <hr>
            <div class="form-floating mb-3">
              <input type="text" class="form-control" name="contact_name" id="contact_name" placeholder="Nama Kontak" value="<?= htmlspecialchars($data['lead']->name ?? ''); ?>" required>
              <label for="contact_name">Nama Kontak</label>
            </div>
            <div class="form-floating mb-3">
              <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($data['lead']->email ?? ''); ?>">
              <label for="email">Email</label>
            </div>
            <div class="form-floating mb-3">
              <input type="tel" class="form-control" name="phone" id="phone" placeholder="Telepon" value="<?= htmlspecialchars($data['lead']->phone ?? ''); ?>">
              <label for="phone">Telepon</label>
            </div>
          </div>

          <!-- Kolom Kanan: Kesepakatan -->
          <div class="col-lg-6">
            <h5><i class="bi bi-briefcase-fill me-2"></i>Data Kesepakatan Baru</h5>
            <hr>
            <div class="form-floating mb-3">
              <input type="text" class="form-control" name="deal_name" id="deal_name" placeholder="Nama Kesepakatan" value="Deal dengan <?= htmlspecialchars($data['lead']->company_name ?? ''); ?>" required>
              <label for="deal_name">Nama Kesepakatan</label>
            </div>
            <div class="form-floating mb-3">
              <input type="number" class="form-control" name="deal_value" id="deal_value" placeholder="Nilai (Rp)" value="0">
              <label for="deal_value">Nilai (Rp)</label>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
          <a href="<?= BASE_URL; ?>/leads" class="btn btn-secondary me-2">Batal</a>
          <button type="submit" class="btn btn-success"><i class="bi bi-check2-circle me-2"></i>Konversi Prospek Ini</button>
        </div>
      </form>
    </div>
  </div>
</div>