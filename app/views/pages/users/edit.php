<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Edit Pengguna</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/users">Manajemen Pengguna</a></li>
      <li class="breadcrumb-item active">Edit</li>
    </ol>
  </div>

  <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
      <i class="bi bi-pencil-square me-1"></i>
      Formulir Edit Pengguna
    </div>
    <div class="card-body">
      <form action="<?= BASE_URL; ?>/users/edit/<?= $data['id']; ?>" method="POST">
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
          <input type="password" class="form-control <?= (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Password Baru">
          <label for="password">Password Baru</label>
          <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
          <span class="invalid-feedback"><?= $data['password_err']; ?></span>
        </div>

        <div class="d-flex justify-content-end mt-3">
          <a href="<?= BASE_URL; ?>/users" class="btn btn-secondary me-2">Batal</a>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Update Pengguna</button>
        </div>
      </form>
    </div>
  </div>
</div>