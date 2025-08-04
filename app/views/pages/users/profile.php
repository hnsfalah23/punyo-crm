<div class="container-fluid px-4">
  <h1 class="mt-4">Edit Profil</h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Edit Profil</li>
  </ol>

  <?php flash('profile_message'); ?>

  <div class="card mb-4">
    <div class="card-body">
      <form action="<?= BASE_URL; ?>/users/profile" method="post" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-4 text-center">
            <img src="<?= BASE_URL; ?>/uploads/profiles/<?= $data['user']->profile_picture ?? 'default.png'; ?>" alt="Foto Profil" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
            <div class="mb-3">
              <label for="profile_picture" class="form-label">Ubah Foto Profil</label>
              <input class="form-control" type="file" id="profile_picture" name="profile_picture">
            </div>
          </div>
          <div class="col-md-8">
            <div class="mb-3">
              <label for="name" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($data['user']->name); ?>" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($data['user']->email); ?>" required>
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Nomor Telepon</label>
              <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($data['user']->phone ?? ''); ?>">
            </div>
            <hr>
            <p class="text-muted">Kosongkan jika tidak ingin mengubah password.</p>
            <div class="mb-3">
              <label for="password" class="form-label">Password Baru</label>
              <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
              <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            </div>
            <button type="submit" class="btn btn-primary float-end">Simpan Perubahan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>