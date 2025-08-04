<style>
  /* Kustomisasi warna badge status */
  .badge-status-baru {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    border: 1px solid rgba(13, 110, 253, 0.2);
  }

  .badge-status-koordinasi {
    background-color: rgba(255, 193, 7, 0.1);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.2);
  }

  .badge-status-kualifikasi {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754;
    border: 1px solid rgba(25, 135, 84, 0.2);
  }

  .badge-status-non-kualifikasi {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
  }

  .detail-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
  }

  .detail-item:last-child {
    border-bottom: none;
  }

  .detail-label {
    color: #6c757d;
  }

  .detail-value {
    font-weight: 600;
  }

  /* Styling Breadcrumb */
  .breadcrumb-item a {
    text-decoration: none;
    color: #0d6efd;
  }

  .breadcrumb-item a:hover {
    text-decoration: underline;
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
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="mt-4">Detail Prospek</h1>
    <ol class="breadcrumb mb-0 mt-4 bg-transparent">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/prospek">Manajemen Prospek</a></li>
      <li class="breadcrumb-item active"><?= htmlspecialchars($data['lead']->name); ?></li>
    </ol>
  </div>
  <hr>

  <?php flash('activity_message'); ?>
  <?php flash('lead_message'); ?>

  <div class="row">
    <div class="col-lg-5">
      <div class="card mb-4">
        <div class="card-header"><span><i class="bi bi-info-circle-fill me-2"></i>Informasi Prospek & Instansi</span></div>
        <div class="card-body">
          <div class="detail-item"><span class="detail-label">Nama Instansi</span><span class="detail-value"><?= htmlspecialchars($data['lead']->company_name ?? ''); ?></span></div>
          <div class="detail-item"><span class="detail-label">Nama Prospek</span><span class="detail-value"><?= htmlspecialchars($data['lead']->name ?? ''); ?></span></div>
          <div class="detail-item"><span class="detail-label">Email</span><span class="detail-value"><?= htmlspecialchars($data['lead']->email ?? ''); ?></span></div>
          <div class="detail-item"><span class="detail-label">Telepon</span><span class="detail-value"><?= htmlspecialchars($data['lead']->phone ?? ''); ?></span></div>
          <div class="detail-item"><span class="detail-label">Status</span><span class="detail-value"><?php $statusClass = 'badge-status-' . strtolower(str_replace(' ', '-', $data['lead']->status)); ?><span class="badge rounded-pill px-3 py-2 <?= $statusClass; ?>"><?= htmlspecialchars($data['lead']->status ?? ''); ?></span></span></div>
          <div class="detail-item"><span class="detail-label">Sumber</span><span class="detail-value"><?= htmlspecialchars($data['lead']->source ?? ''); ?></span></div>
          <div class="detail-item"><span class="detail-label">Pemilik</span><span class="detail-value"><?= htmlspecialchars($data['lead']->owner_name ?? ''); ?></span></div>
        </div>
        <div class="card-footer text-end">
          <?php if (can('update', 'leads')): ?>
            <button type="button" class="btn btn-warning text-white edit-lead-btn" data-id="<?= $data['lead']->lead_id; ?>"><i class="bi bi-pencil-fill me-1"></i> Edit</button>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas</span>
          <?php if (can('create', 'leads')): ?>
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
                    <small class="text-muted d-block mb-2"><?= date('d M Y, H:i', strtotime($activity->start_time)); ?> oleh <?= htmlspecialchars($activity->owner_name); ?></small>
                    <div class="p-3 bg-light rounded mt-2">
                      <?php if (!empty($activity->description)): ?>
                        <p class="mb-1 fst-italic">"<?= nl2br(htmlspecialchars($activity->description)); ?>"</p>
                      <?php endif; ?>
                      <?php if ($activity->documentation_photo): ?>
                        <?php if (!empty($activity->description)) echo '<hr>'; ?>
                        <a href="<?= BASE_URL; ?>/uploads/activities/<?= $activity->documentation_photo; ?>" target="_blank">
                          <img src="<?= BASE_URL; ?>/uploads/activities/<?= $activity->documentation_photo; ?>" alt="Dokumentasi" class="img-fluid rounded mt-2" style="max-height: 150px;">
                        </a>
                      <?php endif; ?>
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

<!-- Modal Tambah Aktivitas -->
<div class="modal fade" id="addActivityModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Aktivitas Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addActivityForm" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="related_item_id" value="<?= $data['lead']->lead_id; ?>">
          <input type="hidden" name="related_item_type" value="lead">
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
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Prospek -->
<div class="modal fade" id="editLeadModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Prospek</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editLeadForm" method="POST">
        <div class="modal-body">
          <div class="form-floating mb-3"><input type="text" id="edit_name" name="name" class="form-control" placeholder="Nama Prospek" required><label>Nama Prospek</label></div>
          <div class="form-floating mb-3"><input type="text" id="edit_company_name" name="company_name" class="form-control" placeholder="Nama Instansi"><label>Nama Instansi</label></div>
          <div class="form-floating mb-3"><input type="email" id="edit_email" name="email" class="form-control" placeholder="Email"><label>Email</label></div>
          <div class="form-floating mb-3"><input type="tel" id="edit_phone" name="phone" class="form-control" placeholder="Telepon"><label>Telepon</label></div>
          <div class="form-floating mb-3"><select id="edit_status" name="status" class="form-select">
              <option value="Baru">Baru</option>
              <option value="Koordinasi">Koordinasi</option>
              <option value="Kualifikasi">Kualifikasi</option>
              <option value="Non Kualifikasi">Non Kualifikasi</option>
            </select><label>Status</label></div>
          <div class="form-floating mb-3"><input type="text" id="edit_source" name="source" class="form-control" placeholder="Sumber Prospek"><label>Sumber Prospek</label></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Aktivitas -->
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
    const editLeadModal = new bootstrap.Modal(document.getElementById('editLeadModal'));
    const editLeadForm = document.getElementById('editLeadForm');
    const editStatusSelect = document.getElementById('edit_status');
    let originalStatus = '';

    document.querySelectorAll('.edit-lead-btn').forEach(button => {
      button.addEventListener('click', function() {
        const leadId = this.dataset.id;
        fetch(`<?= BASE_URL ?>/prospek/getProspekJson/${leadId}`)
          .then(response => response.json())
          .then(data => {
            if (data) {
              editLeadForm.action = `<?= BASE_URL ?>/prospek/edit/${leadId}`;
              document.getElementById('edit_name').value = data.name;
              document.getElementById('edit_company_name').value = data.company_name;
              document.getElementById('edit_email').value = data.email;
              document.getElementById('edit_phone').value = data.phone;
              editStatusSelect.value = data.status;
              document.getElementById('edit_source').value = data.source;
              originalStatus = data.status;
              editLeadModal.show();
            }
          });
      });
    });

    editLeadForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const newStatus = editStatusSelect.value;
      const submitForm = () => {
        handleAjaxFormSubmit(editLeadForm, editLeadForm.action, 'Data prospek berhasil diperbarui.', editLeadModal);
      };

      if (newStatus === 'Kualifikasi' && originalStatus !== 'Kualifikasi') {
        Swal.fire({
          title: 'Konversi Prospek?',
          html: "Status diubah menjadi <strong>Terkualifikasi</strong>. <br>Prospek ini akan dikonversi.",
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yakin & Konversi',
          cancelButtonText: 'Batal',
        }).then((result) => {
          if (result.isConfirmed) {
            submitForm();
          }
        });
      } else {
        submitForm();
      }
    });

    // --- JAVASCRIPT BARU UNTUK AKTIVITAS ---
    const addActivityModal = new bootstrap.Modal(document.getElementById('addActivityModal'));
    const editActivityModal = new bootstrap.Modal(document.getElementById('editActivityModal'));
    const addActivityForm = document.getElementById('addActivityForm');
    const editActivityForm = document.getElementById('editActivityForm');

    addActivityForm.addEventListener('submit', function(e) {
      e.preventDefault();
      handleAjaxFormSubmit(this, '<?= BASE_URL; ?>/activities/add', 'Aktivitas berhasil ditambahkan.', addActivityModal);
    });

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

    editActivityForm.addEventListener('submit', function(e) {
      e.preventDefault();
      handleAjaxFormSubmit(this, this.action, 'Aktivitas berhasil diperbarui.', editActivityModal);
    });

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
              .then(() => {
                if (result.data && result.data.action === 'redirect') {
                  window.location.href = result.data.url;
                } else {
                  location.reload();
                }
              });
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