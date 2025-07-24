<!-- app/views/pages/register.php -->
<style>
  .input-group-text {
    width: 42px;
    justify-content: center;
  }

  .toggle-password {
    cursor: pointer;
  }
</style>
<div class="container-fluid">
  <div class="row vh-100">
    <div class="col-lg-7 d-none d-lg-block p-0">
      <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white" style="background: linear-gradient(45deg, #28a745, #20c997);">
        <div data-aos="fade-right" data-aos-duration="1000">
          <h1 class="display-3 fw-bold">Bergabunglah</h1>
          <p class="lead">Mulai kelola pelanggan Anda dengan lebih baik.</p>
        </div>
      </div>
    </div>
    <div class="col-lg-5 d-flex justify-content-center align-items-center bg-light">
      <div class="card shadow-lg border-0 rounded-4" style="width: 24rem;" data-aos="fade-left" data-aos-duration="1000">
        <div class="card-body p-4 p-sm-5">
          <h3 class="card-title text-center mb-4 fw-bold">Buat Akun Baru</h3>
          <form action="<?= BASE_URL; ?>/auth/register" method="POST">
            <div class="input-group mb-3">
              <span class="input-group-text"><i class="bi bi-person"></i></span>
              <div class="form-floating">
                <input type="text" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" id="name" name="name" placeholder="Nama Lengkap" value="<?= $data['name']; ?>" required>
                <label for="name">Nama Lengkap</label>
              </div>
              <div class="invalid-feedback d-block ps-1"><?= $data['name_err']; ?></div>
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <div class="form-floating">
                <input type="email" class="form-control <?= (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="nama@contoh.com" value="<?= $data['email']; ?>" required>
                <label for="email">Alamat Email</label>
              </div>
              <div class="invalid-feedback d-block ps-1"><?= $data['email_err']; ?></div>
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <div class="form-floating">
                <input type="password" class="form-control <?= (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" id="password_reg" name="password" placeholder="Password" required>
                <label for="password_reg">Password</label>
              </div>
              <span class="input-group-text toggle-password" data-target="#password_reg"><i class="bi bi-eye-slash"></i></span>
              <div class="invalid-feedback d-block ps-1"><?= $data['password_err']; ?></div>
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
              <div class="form-floating">
                <input type="password" class="form-control <?= (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" id="confirm_password_reg" name="confirm_password" placeholder="Konfirmasi Password" required>
                <label for="confirm_password_reg">Konfirmasi Password</label>
              </div>
              <span class="input-group-text toggle-password" data-target="#confirm_password_reg"><i class="bi bi-eye-slash"></i></span>
              <div class="invalid-feedback d-block ps-1"><?= $data['confirm_password_err']; ?></div>
            </div>
            <div class="d-grid mb-2">
              <button class="btn btn-success btn-lg fw-bold" type="submit">Daftar</button>
            </div>
            <div class="text-center mt-3">
              <small class="text-muted">Sudah punya akun? <a href="<?= BASE_URL; ?>/auth/login" class="text-decoration-none">Login di sini</a></small>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>