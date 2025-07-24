<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Edit Instansi</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/instansi">Manajemen Instansi</a></li>
      <li class="breadcrumb-item active">Edit</li>
    </ol>
  </div>

  <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
      <i class="bi bi-pencil-square me-1"></i>
      Formulir Edit Instansi
    </div>
    <div class="card-body">
      <form action="<?= BASE_URL; ?>/instansi/edit/<?= $data['id']; ?>" method="POST">
        <div class="form-floating mb-3">
          <input type="text" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" id="name" name="name" placeholder="Nama Instansi" value="<?= $data['name']; ?>">
          <label for="name">Nama Instansi</label>
          <span class="invalid-feedback"><?= $data['name_err']; ?></span>
        </div>
        <div class="form-floating mb-3">
          <input type="url" class="form-control" id="website" name="website" placeholder="https://contoh.com" value="<?= $data['website']; ?>">
          <label for="website">Website</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control" id="industry" name="industry" placeholder="Industri" value="<?= $data['industry']; ?>">
          <label for="industry">Industri</label>
        </div>
        <div class="form-floating mb-3">
          <textarea class="form-control" placeholder="Deskripsi" id="description" name="description" style="height: 100px"><?= $data['description']; ?></textarea>
          <label for="description">Deskripsi</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" class="form-control" id="gmaps_location" name="gmaps_location" placeholder="Alamat atau Nama Tempat" value="<?= $data['gmaps_location']; ?>">
          <label for="gmaps_location">Alamat atau Nama Tempat di Google Maps</label>
        </div>

        <div class="d-flex justify-content-end mt-3">
          <a href="<?= BASE_URL; ?>/instansi" class="btn btn-secondary me-2">Batal</a>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Update Instansi</button>
        </div>
      </form>
    </div>
  </div>
</div>