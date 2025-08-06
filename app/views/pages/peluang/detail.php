<style>
  /* Kustomisasi warna badge tahapan */
  .badge-stage {
    padding: 0.5em 0.75em;
    font-weight: 600;
  }

  .badge-stage-analisis-kebutuhan {
    background-color: #cffafe;
    color: #0891b2;
  }

  .badge-stage-proposal {
    background-color: #dbeafe;
    color: #2563eb;
  }

  .badge-stage-negosiasi {
    background-color: #fef3c7;
    color: #d97706;
  }

  .badge-stage-menang {
    background-color: #dcfce7;
    color: #16a34a;
  }

  .badge-stage-kalah {
    background-color: #fee2e2;
    color: #dc2626;
  }

  .detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 0.95rem;
  }

  .detail-item i {
    font-size: 1.2rem;
    width: 30px;
    text-align: center;
    color: #6c757d;
  }

  .detail-item .detail-label {
    color: #6c757d;
    margin-left: 1rem;
  }

  .detail-item .detail-value {
    font-weight: 600;
    margin-left: auto;
    text-align: right;
  }

  /* Timeline Aktivitas */
  .activity-timeline .activity-item {
    position: relative;
    padding-bottom: 2rem;
    padding-left: 35px;
    border-left: 2px solid #e9ecef;
  }

  .activity-timeline .activity-item:last-child {
    border-left: 2px solid transparent;
    padding-bottom: 0;
  }

  .activity-timeline .activity-icon {
    position: absolute;
    left: -15px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #fff;
    border: 2px solid #e9ecef;
  }
</style>

<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4"><?= htmlspecialchars($data['deal']->name); ?></h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/peluang">Peluang</a></li>
      <li class="breadcrumb-item active">Detail</li>
    </ol>
  </div>

  <?php flash('activity_message'); ?>

  <div class="row">
    <div class="col-lg-5">
      <div class="card mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-info-circle-fill me-2"></i>Informasi Utama</span>
          <?php if (can('update', 'deals')): ?>
            <a href="<?= BASE_URL; ?>/peluang/edit/<?= $data['deal']->deal_id; ?>" class="btn btn-warning btn-sm text-white">
              <i class="bi bi-pencil-fill me-1"></i> Edit
            </a>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <div class="detail-item">
            <i class="bi bi-building"></i>
            <span class="detail-label">Instansi</span>
            <span class="detail-value"><a href="<?= BASE_URL; ?>/instansi/detail/<?= $data['deal']->company_id; ?>"><?= htmlspecialchars($data['deal']->company_name); ?></a></span>
          </div>
          <div class="detail-item">
            <i class="bi bi-person-badge"></i>
            <span class="detail-label">Kontak Utama</span>
            <span class="detail-value"><?= htmlspecialchars($data['deal']->contact_name); ?></span>
          </div>
          <div class="detail-item">
            <i class="bi bi-person-fill"></i>
            <span class="detail-label">Pemilik</span>
            <span class="detail-value"><?= htmlspecialchars($data['deal']->owner_name); ?></span>
          </div>
          <div class="detail-item">
            <i class="bi bi-bar-chart-steps"></i>
            <span class="detail-label">Tahapan</span>
            <span class="detail-value">
              <?php $stageClass = 'badge-stage-' . strtolower(str_replace(' ', '-', $data['deal']->stage)); ?>
              <span class="badge rounded-pill <?= $stageClass; ?>"><?= htmlspecialchars($data['deal']->stage); ?></span>
            </span>
          </div>
          <div class="detail-item">
            <i class="bi bi-cash-coin"></i>
            <span class="detail-label">Nilai Peluang</span>
            <span class="detail-value">Rp <?= number_format($data['deal']->value, 0, ',', '.'); ?></span>
          </div>
          <div class="detail-item">
            <i class="bi bi-calendar-check"></i>
            <span class="detail-label">Perkiraan Pembayaran</span>
            <span class="detail-value"><?= $data['deal']->expected_close_date ? date('d F Y', strtotime($data['deal']->expected_close_date)) : 'N/A'; ?></span>
          </div>
        </div>
      </div>
      <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-header"><i class="bi bi-box-seam-fill me-2"></i>Produk Terkait</div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <?php if (empty($data['products'])): ?>
              <li class="list-group-item text-center text-muted">Belum ada produk.</li>
            <?php else: ?>
              <?php foreach ($data['products'] as $product): ?>
                <li class="list-group-item d-flex justify-content-between">
                  <span><?= htmlspecialchars($product->name); ?> (<?= $product->quantity; ?>x)</span>
                  <span>Rp <?= number_format($product->price_per_unit * $product->quantity, 0, ',', '.'); ?></span>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card mb-4" data-aos="fade-up" data-aos-delay="300">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas</span>
          <?php if (can('create', 'deals')): ?>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addActivityModal"><i class="bi bi-plus-lg me-1"></i> Tambah</button>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <?php if (empty($data['activities'])): ?>
            <p class="text-center text-muted py-5">Belum ada aktivitas tercatat.</p>
          <?php else: ?>
            <div class="activity-timeline">
              <?php foreach ($data['activities'] as $activity): ?>
                <div class="activity-item">
                  <div class="activity-icon"><i class="bi bi-calendar-event"></i></div>
                  <div class="w-100">
                    <div class="d-flex justify-content-between">
                      <h6 class="mb-0"><?= htmlspecialchars($activity->name); ?></h6>
                      <div>
                        <?php if (can('update', 'activities')): ?>
                          <button class="btn btn-sm btn-outline-warning border-0 p-1 edit-activity-btn" data-id="<?= $activity->activity_id ?>"><i class="bi bi-pencil-fill"></i></button>
                        <?php endif; ?>
                        <?php if (can('delete', 'activities')): ?>
                          <button class="btn btn-sm btn-outline-danger border-0 p-1 delete-activity-btn" data-id="<?= $activity->activity_id ?>" data-name="<?= htmlspecialchars($activity->name) ?>"><i class="bi bi-trash"></i></button>
                        <?php endif; ?>
                      </div>
                    </div>
                    <small class="text-muted d-block mb-2">Oleh: <?= htmlspecialchars($activity->owner_name); ?> pada <?= date('d M Y, H:i', strtotime($activity->start_time)); ?></small>
                    <div class="p-3 bg-light rounded mt-2">
                      <?php if (!empty($activity->description)): ?><p class="mb-1 fst-italic">"<?= nl2br(htmlspecialchars($activity->description)); ?>"</p><?php endif; ?>
                      <?php if ($activity->documentation_photo): ?><?php if (!empty($activity->description)) echo '<hr>'; ?><a href="<?= BASE_URL; ?>/uploads/activities/<?= $activity->documentation_photo; ?>" target="_blank"><img src="<?= BASE_URL; ?>/uploads/activities/<?= $activity->documentation_photo; ?>" alt="Dokumentasi" class="img-fluid rounded mt-2" style="max-height: 150px;"></a><?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="addActivityModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Aktivitas Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addActivityForm" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="related_item_id" value="<?= $data['deal']->deal_id; ?>">
          <input type="hidden" name="related_item_type" value="deal">
          <div class="mb-3"><label class="form-label">Nama Aktivitas</label><input type="text" class="form-control" name="name" required></div>
          <div class="mb-3"><label class="form-label">Jenis</label><select name="type" class="form-select" required>
              <option value="Tugas">Tugas</option>
              <option value="Panggilan">Panggilan</option>
              <option value="Email">Email</option>
              <option value="Rapat">Rapat</option>
            </select></div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" class="form-control" name="start_date" onclick="this.showPicker()" value="<?= date('Y-m-d'); ?>" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Waktu Mulai</label><input type="time" class="form-control" name="start_time" onclick="this.showPicker()" value="<?= date('H:i'); ?>" required></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Selesai (Opsional)</label><input type="date" class="form-control" name="end_date" onclick="this.showPicker()"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Waktu Selesai (Opsional)</label><input type="time" class="form-control" name="end_time" onclick="this.showPicker()"></div>
          </div>
          <div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="3"></textarea></div>
          <div class="mb-3"><label class="form-label">Foto Dokumentasi (Opsional)</label><input class="form-control" type="file" name="documentation_photo" accept="image/*"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Aktivitas</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editActivityModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Aktivitas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editActivityForm" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Nama Aktivitas</label><input type="text" id="edit_activity_name" class="form-control" name="name" required></div>
          <div class="mb-3"><label class="form-label">Jenis</label><select id="edit_activity_type" name="type" class="form-select" required>
              <option value="Tugas">Tugas</option>
              <option value="Panggilan">Panggilan</option>
              <option value="Email">Email</option>
              <option value="Rapat">Rapat</option>
            </select></div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" id="edit_start_date" class="form-control" name="start_date" onclick="this.showPicker()" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Waktu Mulai</label><input type="time" id="edit_start_time" class="form-control" name="start_time" onclick="this.showPicker()" required></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Selesai (Opsional)</label><input type="date" id="edit_end_date" class="form-control" name="end_date" onclick="this.showPicker()"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Waktu Selesai (Opsional)</label><input type="time" id="edit_end_time" class="form-control" name="end_time" onclick="this.showPicker()"></div>
          </div>
          <div class="mb-3"><label class="form-label">Deskripsi</label><textarea id="edit_description" class="form-control" name="description" rows="3"></textarea></div>
          <div class="mb-3"><label class="form-label">Ganti Foto Dokumentasi (Opsional)</label><input class="form-control" type="file" name="documentation_photo" accept="image/*">
            <div id="current_photo_container" class="mt-2"></div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const addActivityModal = new bootstrap.Modal(document.getElementById('addActivityModal'));
    const editActivityModal = new bootstrap.Modal(document.getElementById('editActivityModal'));
    const addActivityForm = document.getElementById('addActivityForm');
    const editActivityForm = document.getElementById('editActivityForm');

    // Handle Tambah Aktivitas
    addActivityForm.addEventListener('submit', function(e) {
      e.preventDefault();
      handleAjaxFormSubmit(this, '<?= BASE_URL; ?>/activities/add', 'Aktivitas berhasil ditambahkan.', addActivityModal);
    });

    // Handle Edit Aktivitas (Membuka Modal)
    document.querySelectorAll('.edit-activity-btn').forEach(button => {
      button.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch(`<?= BASE_URL ?>/activities/getActivityJson/${id}`)
          .then(response => response.json())
          .then(result => {
            if (result.success) {
              const data = result.data;
              editActivityForm.action = `<?= BASE_URL ?>/activities/edit/${id}`;
              document.getElementById('edit_activity_name').value = data.name;
              document.getElementById('edit_activity_type').value = data.type;
              document.getElementById('edit_start_date').value = data.start_time.split(' ')[0];
              document.getElementById('edit_start_time').value = data.start_time.split(' ')[1].substring(0, 5);
              document.getElementById('edit_end_date').value = data.end_time ? data.end_time.split(' ')[0] : '';
              document.getElementById('edit_end_time').value = data.end_time ? data.end_time.split(' ')[1].substring(0, 5) : '';
              document.getElementById('edit_description').value = data.description;

              const photoContainer = document.getElementById('current_photo_container');
              if (data.documentation_photo) {
                photoContainer.innerHTML = `<p class="mb-1 small">Foto saat ini:</p><a href="<?= BASE_URL ?>/uploads/activities/${data.documentation_photo}" target="_blank"><img src="<?= BASE_URL ?>/uploads/activities/${data.documentation_photo}" style="max-height: 100px;" class="img-fluid rounded"></a>`;
              } else {
                photoContainer.innerHTML = '';
              }
              editActivityModal.show();
            } else {
              Swal.fire('Error', result.message, 'error');
            }
          });
      });
    });

    // Handle Submit Form Edit Aktivitas
    editActivityForm.addEventListener('submit', function(e) {
      e.preventDefault();
      handleAjaxFormSubmit(this, this.action, 'Aktivitas berhasil diperbarui.', editActivityModal);
    });

    // Handle Hapus Aktivitas
    document.querySelectorAll('.delete-activity-btn').forEach(button => {
      button.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        Swal.fire({
          title: 'Apakah Anda yakin?',
          text: `Anda akan menghapus aktivitas "${name}".`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
          if (result.isConfirmed) {
            fetch(`<?= BASE_URL ?>/activities/delete/${id}`, {
                method: 'POST'
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  Swal.fire({
                      icon: 'success',
                      title: 'Dihapus!',
                      text: data.message,
                      timer: 1500,
                      showConfirmButton: false
                    })
                    .then(() => location.reload());
                } else {
                  Swal.fire('Gagal!', data.message, 'error');
                }
              });
          }
        });
      });
    });

    // Fungsi helper untuk submit form AJAX
    function handleAjaxFormSubmit(form, url, successMessage, modalInstance) {
      const formData = new FormData(form);
      const submitButton = form.querySelector('button[type="submit"]');

      submitButton.disabled = true;
      submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;

      fetch(url, {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(result => {
          modalInstance.hide();
          if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: successMessage,
                timer: 2000,
                showConfirmButton: false
              })
              .then(() => location.reload());
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
          submitButton.innerHTML = 'Simpan';
        });
    }
  });
</script>