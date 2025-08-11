<style>
  .form-card {
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
    border: none;
  }
</style>

<div class="container-fluid px-4">
  <h1 class="mt-4">Lengkapi Data Konversi</h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/prospek">Manajemen Prospek</a></li>
    <li class="breadcrumb-item active">Lengkapi Data</li>
  </ol>

  <div class="alert alert-success">
    <i class="bi bi-check-circle-fill me-2"></i>
    Prospek berhasil dikonversi! Silakan lengkapi detail di bawah ini.
  </div>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card form-card h-100">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-briefcase-fill me-2"></i>Data Peluang</h5>
        </div>
        <div class="card-body">
          <form id="formPeluang">
            <input type="hidden" name="deal_id" value="<?= $data['peluang']->deal_id; ?>">
            <div class="form-floating mb-3">
              <input type="text" name="name" class="form-control" placeholder="Nama Peluang" value="<?= htmlspecialchars($data['peluang']->name); ?>" required>
              <label>Nama Peluang</label>
            </div>
            <div class="form-floating mb-3">
              <input type="number" name="value" class="form-control" placeholder="Nilai Peluang" value="<?= htmlspecialchars($data['peluang']->value); ?>">
              <label>Nilai (Rp)</label>
            </div>
            <div class="form-floating mb-3">
              <select name="stage" class="form-select">
                <option value="Analisis Kebutuhan" <?= $data['peluang']->stage == 'Analisis Kebutuhan' ? 'selected' : ''; ?>>Analisis Kebutuhan</option>
                <option value="Proposal" <?= $data['peluang']->stage == 'Proposal' ? 'selected' : ''; ?>>Proposal</option>
                <option value="Negosiasi" <?= $data['peluang']->stage == 'Negosiasi' ? 'selected' : ''; ?>>Negosiasi</option>
              </select>
              <label>Tahapan</label>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Simpan Peluang</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card form-card h-100">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-building me-2"></i>Data Instansi</h5>
        </div>
        <div class="card-body">
          <form id="formInstansi">
            <input type="hidden" name="company_id" value="<?= $data['instansi']->company_id; ?>">
            <div class="form-floating mb-3">
              <input type="text" name="name" class="form-control" placeholder="Nama Instansi" value="<?= htmlspecialchars($data['instansi']->name); ?>" required>
              <label>Nama Instansi</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" name="industry" class="form-control" placeholder="Industri" value="<?= htmlspecialchars($data['instansi']->industry); ?>">
              <label>Industri</label>
            </div>
            <div class="form-floating mb-3">
              <input type="url" name="website" class="form-control" placeholder="Website" value="<?= htmlspecialchars($data['instansi']->website); ?>">
              <label>Website</label>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Simpan Instansi</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card form-card h-100">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-person-fill me-2"></i>Data Kontak</h5>
        </div>
        <div class="card-body">
          <form id="formKontak">
            <input type="hidden" name="contact_id" value="<?= $data['kontak']->contact_id; ?>">
            <div class="form-floating mb-3">
              <input type="text" name="name" class="form-control" placeholder="Nama Kontak" value="<?= htmlspecialchars($data['kontak']->name); ?>" required>
              <label>Nama Kontak</label>
            </div>
            <div class="form-floating mb-3">
              <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($data['kontak']->email); ?>">
              <label>Email</label>
            </div>
            <div class="form-floating mb-3">
              <input type="tel" name="phone" class="form-control" placeholder="Telepon" value="<?= htmlspecialchars($data['kontak']->phone); ?>">
              <label>Telepon</label>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Simpan Kontak</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="text-center mt-4">
    <a href="<?= BASE_URL; ?>/instansi/detail/<?= $data['instansi']->company_id; ?>" class="btn btn-success btn-lg">
      <i class="bi bi-check-all me-2"></i>Selesai & Lihat Detail Instansi
    </a>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Fungsi helper untuk menangani submit form AJAX
    const handleFormSubmit = async (form, url, successMessage) => {
      const submitButton = form.querySelector('button[type="submit"]');
      const originalButtonText = submitButton.innerHTML;
      submitButton.disabled = true;
      submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;

      try {
        const formData = new FormData(form);
        const response = await fetch(url, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest' // Tambahkan header ini
          }
        });
        const result = await response.json();

        if (result.success) {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: successMessage,
            timer: 2000,
            showConfirmButton: false
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: result.message || 'Terjadi kesalahan.'
          });
        }
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Tidak dapat terhubung ke server.'
        });
      } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
      }
    };

    // Event listener untuk setiap form
    document.getElementById('formPeluang').addEventListener('submit', function(e) {
      e.preventDefault();
      const dealId = this.querySelector('input[name="deal_id"]').value;
      handleFormSubmit(this, `<?= BASE_URL; ?>/peluang/edit/${dealId}`, 'Data peluang berhasil diperbarui.');
    });

    document.getElementById('formInstansi').addEventListener('submit', function(e) {
      e.preventDefault();
      handleFormSubmit(this, '<?= BASE_URL; ?>/instansi/edit', 'Data instansi berhasil diperbarui.');
    });

    document.getElementById('formKontak').addEventListener('submit', function(e) {
      e.preventDefault();
      handleFormSubmit(this, '<?= BASE_URL; ?>/kontak/edit', 'Data kontak berhasil diperbarui.');
    });
  });
</script>