<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Edit Prospek</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/leads">Manajemen Prospek</a></li>
      <li class="breadcrumb-item active">Edit</li>
    </ol>
  </div>

  <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
      <i class="bi bi-pencil-square me-1"></i>
      Formulir Edit Prospek
    </div>
    <div class="card-body">
      <form action="<?= BASE_URL; ?>/leads/edit/<?= $data['id']; ?>" method="POST">
        <div class="row gx-4">
          <div class="col-lg-6">
            <h5><i class="bi bi-person-badge me-2"></i>Informasi Kontak Prospek</h5>
            <hr>
            <div class="form-floating mb-3">
              <input type="text" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" id="name" name="name" placeholder="Nama Prospek" value="<?= $data['name']; ?>">
              <label for="name">Nama Prospek</label>
              <span class="invalid-feedback"><?= $data['name_err']; ?></span>
            </div>
            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?= $data['email']; ?>">
              <label for="email">Email</label>
            </div>
            <div class="form-floating mb-3">
              <input type="tel" class="form-control" id="phone" name="phone" placeholder="Telepon" value="<?= $data['phone']; ?>">
              <label for="phone">Telepon</label>
            </div>
          </div>

          <div class="col-lg-6">
            <h5><i class="bi bi-building me-2"></i>Informasi Perusahaan</h5>
            <hr>
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Nama Instansi" value="<?= $data['company_name']; ?>">
              <label for="company_name">Nama Instansi</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="source" name="source" placeholder="Sumber Prospek" value="<?= $data['source']; ?>">
              <label for="source">Sumber Prospek (e.g., Website, Referensi)</label>
            </div>
            <div class="form-floating mb-3">
              <select name="status" id="status" class="form-select">
                <option value="Baru" <?= ($data['status'] == 'Baru') ? 'selected' : ''; ?>>Baru</option>
                <option value="Koordinasi" <?= ($data['status'] == 'Koordinasi') ? 'selected' : ''; ?>>Koordinasi</option>
                <option value="Kualifikasi" <?= ($data['status'] == 'Kualifikasi') ? 'selected' : ''; ?>>Kualifikasi</option>
                <option value="Non Kualifikasi" <?= ($data['status'] == 'Non Kualifikasi') ? 'selected' : ''; ?>>Non Kualifikasi</option>
              </select>
              <label for="status">Status</label>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
          <a href="<?= BASE_URL; ?>/leads" class="btn btn-secondary me-2">Batal</a>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Update Prospek</button>
        </div>
      </form>
    </div>
  </div>
</div>