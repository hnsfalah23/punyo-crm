<!-- app/views/pages/kontak/edit.php -->
<div class="container-fluid px-4">
  <h1 class="mt-4">Edit Kontak untuk <?= htmlspecialchars($data['instansi']->name); ?></h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/instansi">Manajemen Instansi</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/instansi/detail/<?= $data['instansi']->company_id; ?>">Detail</a></li>
    <li class="breadcrumb-item active">Edit Kontak</li>
  </ol>
  <div class="card mb-4">
    <div class="card-header">
      <p>Form Edit Kontak</p>
    </div>
    <div class="card-body">
      <form action="<?= BASE_URL; ?>/kontak/edit/<?= $data['id']; ?>" method="POST">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="name" class="form-label">Nama Kontak</label>
            <input type="text" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" name="name" value="<?= $data['name']; ?>">
            <span class="invalid-feedback"><?= $data['name_err']; ?></span>
          </div>
          <div class="col-md-6 mb-3">
            <label for="job_title" class="form-label">Jabatan</label>
            <input type="text" class="form-control" name="job_title" value="<?= $data['job_title']; ?>">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= $data['email']; ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="phone" class="form-label">Telepon</label>
            <input type="tel" class="form-control" name="phone" value="<?= $data['phone']; ?>">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="contact_type" class="form-label">Jenis Kontak</label>
            <input type="text" class="form-control" name="contact_type" value="<?= $data['contact_type']; ?>" placeholder="e.g., Pengambil Keputusan">
          </div>
          <div class="col-md-6 mb-3">
            <label for="priority" class="form-label">Prioritas</label>
            <select name="priority" class="form-select">
              <option value="Sedang" <?= ($data['priority'] == 'Sedang') ? 'selected' : ''; ?>>Sedang</option>
              <option value="Tinggi" <?= ($data['priority'] == 'Tinggi') ? 'selected' : ''; ?>>Tinggi</option>
              <option value="Rendah" <?= ($data['priority'] == 'Rendah') ? 'selected' : ''; ?>>Rendah</option>
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Kontak</button>
        <a href="<?= BASE_URL; ?>/instansi/detail/<?= $data['instansi']->company_id; ?>" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
</div>