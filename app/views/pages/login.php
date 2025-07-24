<!-- app/views/pages/login.php -->
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
    <!-- Bagian Kiri (Branding) -->
    <div class="col-lg-7 d-none d-lg-block p-0">
      <div class="d-flex flex-column justify-content-center align-items-center h-100 text-white" style="background: linear-gradient(45deg, #0d6efd, #00c6ff);">
        <div data-aos="fade-right" data-aos-duration="1000">
          <h1 class="display-3 fw-bold">Punyo CRM</h1>
          <p class="lead">Solusi Manajemen Pelanggan Anda.</p>
        </div>
      </div>
    </div>

    <!-- Bagian Kanan (Form Login) -->
    <div class="col-lg-5 d-flex justify-content-center align-items-center bg-light">
      <div class="card shadow-lg border-0 rounded-4" style="width: 24rem;" data-aos="fade-left" data-aos-duration="1000">
        <div class="card-body p-4 p-sm-5">
          <h3 class="card-title text-center mb-4 fw-bold">Login</h3>

          <?php flash('register_success'); ?>

          <form action="<?= BASE_URL; ?>/auth/login" method="POST">
            <?php if (!empty($data['error'])): ?>
              <div class="alert alert-danger" role="alert"><?= htmlspecialchars($data['error']); ?></div>
            <?php endif; ?>

            <div class="input-group mb-3">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" placeholder="nama@contoh.com" required>
                <label for="email">Alamat Email</label>
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Password</label>
              </div>
              <!-- Perbaikan: Pastikan menggunakan class="toggle-password" dan data-target -->
              <span class="input-group-text toggle-password" data-target="#password"><i class="bi bi-eye-slash"></i></span>
            </div>

            <div class="d-grid mb-2">
              <button class="btn btn-primary btn-lg fw-bold" type="submit">Masuk</button>
            </div>

            <div class="text-center mt-3">
              <small class="text-muted">Belum punya akun? <a href="<?= BASE_URL; ?>/auth/register" class="text-decoration-none">Daftar sekarang</a></small>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>