<style>
  body {
    background-color: #f8f9fa;
  }

  .detail-card {
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
    border: none;
  }

  .detail-header {
    background: linear-gradient(135deg, #0d6efd, #0d6efd);
    color: white;
    padding: 2rem;
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
  }

  .detail-header h1 {
    font-weight: 700;
    margin-bottom: 0.25rem;
  }

  .detail-header .badge {
    font-size: 0.9rem;
  }

  .info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
  }

  .info-item i {
    font-size: 1.5rem;
    color: #0d6efd;
    width: 40px;
    text-align: center;
    margin-right: 1rem;
    margin-top: 5px;
  }

  .section-title {
    font-weight: 600;
    color: #343a40;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #0d6efd;
    display: inline-block;
  }

  .table-hover tbody tr:hover {
    background-color: #f1f1f1;
  }

  .product-card {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    transition: all 0.2s ease-in-out;
  }

  .product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .product-card .card-img-left {
    width: 120px;
    height: 100%;
    object-fit: cover;
    border-top-left-radius: 0.5rem;
    border-bottom-left-radius: 0.5rem;
  }

  /* Warna Badge Kustom */
  .bg-purple {
    color: #fff;
    background-color: #8e44ad !important;
  }

  .action-btn-sm {
    width: 30px;
    height: 30px;
    font-size: 0.8rem;
  }
</style>

<div class="container-fluid px-4 py-5">
  <div class="card detail-card">
    <div class="detail-header text-center">
      <h1><?= htmlspecialchars($data['instansi']->name); ?></h1>
      <span class="badge bg-light text-dark"><?= htmlspecialchars($data['instansi']->industry); ?></span>
    </div>

    <div class="card-body p-4 p-md-5">
      <!-- Bagian Informasi Utama -->
      <div class="row">
        <div class="col-lg-7">
          <h4 class="section-title">Informasi Detail</h4>
          <div class="info-item">
            <i class="bi bi-globe"></i>
            <div>
              <strong>Website:</strong><br>
              <a href="<?= htmlspecialchars($data['instansi']->website); ?>" target="_blank"><?= htmlspecialchars($data['instansi']->website); ?></a>
            </div>
          </div>
          <div class="info-item">
            <i class="bi bi-file-text-fill"></i>
            <div>
              <strong>Deskripsi:</strong><br>
              <p class="mb-0"><?= nl2br(htmlspecialchars($data['instansi']->description)); ?></p>
            </div>
          </div>
          <div class="info-item">
            <i class="bi bi-geo-alt-fill"></i>
            <div>
              <strong>Lokasi:</strong><br>
              <?php
              $gmaps_location = $data['instansi']->gmaps_location;
              if (!empty($gmaps_location)):
                $searchUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($gmaps_location);
              ?>
                <a href="<?= $searchUrl; ?>" target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                  <i class="bi bi-map me-2"></i>Buka di Google Maps
                </a>
              <?php else: ?>
                <span class="text-muted">Lokasi tidak tersedia.</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <h4 class="section-title">Ringkasan</h4>
          <div class="list-group">
            <div class="list-group-item d-flex justify-content-between align-items-center">Total Kontak <span class="badge bg-primary rounded-pill"><?= count($data['kontak']); ?></span></div>
            <div class="list-group-item d-flex justify-content-between align-items-center">Total Peluang <span class="badge bg-success rounded-pill"><?= count($data['deals']); ?></span></div>
            <div class="list-group-item d-flex justify-content-between align-items-center">Total Produk <span class="badge bg-info rounded-pill"><?= count($data['products']); ?></span></div>
          </div>
        </div>
      </div>

      <hr class="my-5">

      <!-- Bagian Kontak Terkait -->
      <div class="mb-5">
        <h4 class="section-title">Kontak Terkait</h4>
        <?php if (empty($data['kontak'])): ?>
          <p>Tidak ada kontak yang terkait.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Telepon</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data['kontak'] as $kontak): ?>
                  <tr>
                    <td><?= htmlspecialchars($kontak->name); ?></td>
                    <td><?= htmlspecialchars($kontak->email); ?></td>
                    <td><?= htmlspecialchars($kontak->phone); ?></td>
                    <td class="text-center">
                      <?php if (can('update', 'kontak')): ?>
                        <button type="button" class="btn btn-warning btn-sm action-btn-sm text-white" title="Edit Kontak" onclick="openEditContactModal(<?= $kontak->contact_id; ?>)">
                          <i class="bi bi-pencil-fill"></i>
                        </button>
                      <?php endif; ?>
                      <?php if (can('delete', 'kontak')): ?>
                        <button type="button" class="btn btn-danger btn-sm action-btn-sm btn-delete-contact" title="Hapus Kontak" data-id="<?= $kontak->contact_id; ?>" data-name="<?= htmlspecialchars($kontak->name); ?>">
                          <i class="bi bi-trash-fill"></i>
                        </button>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

      <!-- Bagian Peluang dan Produk -->
      <div class="row">
        <div class="col-md-6">
          <h4 class="section-title">Peluang Terkait</h4>
          <?php if (empty($data['deals'])): ?>
            <p>Tidak ada peluang yang terkait.</p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Nama Peluang</th>
                    <th>Tahap</th>
                    <th>Nilai</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($data['deals'] as $deal): ?>
                    <tr>
                      <td><?= htmlspecialchars($deal->name); ?></td>
                      <td>
                        <?php
                        $badge_class = 'bg-secondary'; // Warna default
                        if (isset($deal->stage) && $deal->stage) {
                          $lowerStage = strtolower($deal->stage);
                          if (strpos($lowerStage, 'analisis') !== false) $badge_class = 'bg-primary';
                          elseif (strpos($lowerStage, 'proposal') !== false) $badge_class = 'bg-purple';
                          elseif (strpos($lowerStage, 'negosiasi') !== false) $badge_class = 'bg-warning text-dark';
                          elseif (strpos($lowerStage, 'menang') !== false) $badge_class = 'bg-success';
                          elseif (strpos($lowerStage, 'kalah') !== false) $badge_class = 'bg-danger';
                        }
                        ?>
                        <span class="badge <?= $badge_class; ?>"><?= htmlspecialchars($deal->stage ?? 'N/A'); ?></span>
                      </td>
                      <td>Rp <?= number_format($deal->value, 0, ',', '.'); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
        <div class="col-md-6">
          <h4 class="section-title">Produk Terkait</h4>
          <?php if (empty($data['products'])): ?>
            <p>Tidak ada produk yang terkait.</p>
          <?php else: ?>
            <div class="row g-3">
              <?php foreach ($data['products'] as $product): ?>
                <div class="col-12">
                  <div class="card product-card h-100 flex-row">
                    <img src="<?= htmlspecialchars($product->image_url ?? 'https://placehold.co/150x150/0d6efd/white?text=Produk'); ?>" class="card-img-left" alt="<?= htmlspecialchars($product->name); ?>">
                    <div class="card-body">
                      <h6 class="card-title"><?= htmlspecialchars($product->name); ?></h6>
                      <span class="fw-bold text-primary">Rp <?= number_format($product->price, 0, ',', '.'); ?></span>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="text-center mt-5">
        <a href="<?= BASE_URL; ?>/instansi" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar</a>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Kontak -->
<div class="modal fade" id="editContactModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Kontak</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editContactForm">
        <input type="hidden" name="contact_id" id="edit_contact_id">
        <div class="modal-body">
          <div class="form-floating mb-3"><input type="text" name="name" id="edit_contact_name" class="form-control" placeholder="Nama Kontak" required><label for="edit_contact_name">Nama Kontak</label></div>
          <div class="form-floating mb-3"><input type="email" name="email" id="edit_contact_email" class="form-control" placeholder="Email"><label for="edit_contact_email">Email</label></div>
          <div class="form-floating mb-3"><input type="tel" name="phone" id="edit_contact_phone" class="form-control" placeholder="Telepon"><label for="edit_contact_phone">Telepon</label></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const editContactForm = document.getElementById('editContactForm');

    // Event listener untuk submit form edit kontak
    editContactForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const submitButton = this.querySelector('button[type="submit"]');
      submitButton.disabled = true;

      const formData = new FormData(this);
      fetch('<?= BASE_URL; ?>/kontak/edit', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              text: result.message,
              timer: 1500,
              showConfirmButton: false
            });
            setTimeout(() => location.reload(), 1500);
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: result.message
            });
          }
        })
        .catch(() => Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Tidak dapat terhubung ke server.'
        }))
        .finally(() => {
          submitButton.disabled = false;
        });
    });

    // Event listener untuk tombol hapus kontak
    document.querySelectorAll('.btn-delete-contact').forEach(button => {
      button.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;

        Swal.fire({
          title: 'Apakah Anda yakin?',
          text: `Anda akan menghapus kontak "${name}".`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            fetch(`<?= BASE_URL; ?>/kontak/delete/${id}`, {
                method: 'POST'
              })
              .then(response => response.json())
              .then(res => {
                if (res.success) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Dihapus!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                  });
                  setTimeout(() => location.reload(), 1500);
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: res.message
                  });
                }
              });
          }
        });
      });
    });
  });

  // Fungsi untuk membuka modal edit kontak
  async function openEditContactModal(id) {
    const form = document.getElementById('editContactForm');
    form.reset();

    const modalEl = document.getElementById('editContactModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);

    try {
      const response = await fetch(`<?= BASE_URL; ?>/kontak/getKontakData/${id}`);
      const result = await response.json();

      if (result.success) {
        const data = result.data;
        document.getElementById('edit_contact_id').value = data.contact_id;
        document.getElementById('edit_contact_name').value = data.name;
        document.getElementById('edit_contact_email').value = data.email || '';
        document.getElementById('edit_contact_phone').value = data.phone || '';
        modal.show();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal Memuat Data',
          text: result.message
        });
      }
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Tidak dapat mengambil data dari server.'
      });
    }
  }
</script>