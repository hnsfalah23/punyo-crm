<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Tambah Pengguna Baru</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/users">Manajemen Pengguna</a></li>
      <li class="breadcrumb-item active">Tambah</li>
    </ol>
  </div>

  <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
      <i class="bi bi-person-plus-fill me-1"></i>
      Formulir Pengguna Baru
    </div>
    <div class="card-body">
      <form action="<?= BASE_URL; ?>/users/add" method="POST">
        <div class="form-floating mb-3">
          <input type="text" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" id="name" name="name" placeholder="Nama Pengguna" value="<?= $data['name']; ?>">
          <label for="name">Nama Pengguna</label>
          <span class="invalid-feedback"><?= $data['name_err']; ?></span>
        </div>
        <div class="form-floating mb-3">
          <input type="email" class="form-control <?= (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="Email" value="<?= $data['email']; ?>">
          <label for="email">Email</label>
          <span class="invalid-feedback"><?= $data['email_err']; ?></span>
        </div>
        <div class="form-floating mb-3">
          <select name="role_id" id="role_id" class="form-select <?= (!empty($data['role_id_err'])) ? 'is-invalid' : ''; ?>">
            <option value="">Pilih Peran...</option>
            <?php foreach ($data['roles'] as $role): ?>
              <option value="<?= $role->role_id; ?>" <?= ($data['role_id'] == $role->role_id) ? 'selected' : ''; ?>><?= $role->role_name; ?></option>
            <?php endforeach; ?>
          </select>
          <label for="role_id">Peran (Role)</label>
          <span class="invalid-feedback"><?= $data['role_id_err']; ?></span>
        </div>
        <hr>
        <div class="form-floating mb-3">
          <input type="password" class="form-control <?= (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Password">
          <label for="password">Password</label>
          <span class="invalid-feedback"><?= $data['password_err']; ?></span>
        </div>
        <div class="form-floating mb-3">
          <input type="password" class="form-control <?= (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password">
          <label for="confirm_password">Konfirmasi Password</label>
          <span class="invalid-feedback"><?= $data['confirm_password_err']; ?></span>
        </div>

        <div class="d-flex justify-content-end mt-3">
          <a href="<?= BASE_URL; ?>/users" class="btn btn-secondary me-2">Batal</a>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan Pengguna</button>
        </div>
      </form>
    </div>
  </div>
</div>