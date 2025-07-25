<style>
  /* Kustomisasi warna badge tahapan */
  .badge-stage {
    padding: 0.5em 0.75em;
    font-weight: 600;
  }

  .badge-stage-kualifikasi {
    background-color: #e9d5ff;
    color: #9333ea;
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
</style>

<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4"><?= htmlspecialchars($data['deal']->name); ?></h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/deals">Kesepakatan</a></li>
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
            <a href="<?= BASE_URL; ?>/deals/edit/<?= $data['deal']->deal_id; ?>" class="btn btn-warning btn-sm text-white">
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
            <span class="detail-label">Nilai Kesepakatan</span>
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
            <?php foreach ($data['activities'] as $activity): ?>
              <div class="d-flex mb-3 pb-3 border-bottom">
                <div class="me-3 text-center">
                  <i class="bi bi-calendar-event fs-2 text-primary"></i>
                  <div class="text-muted small"><?= date('d M', strtotime($activity->start_time)); ?></div>
                </div>
                <div class="w-100">
                  <div class="d-flex justify-content-between">
                    <h6 class="mb-0"><?= htmlspecialchars($activity->name); ?> <span class="badge bg-secondary fw-normal"><?= htmlspecialchars($activity->type); ?></span></h6>
                    <?php if (can('update', 'deals') || can('delete', 'deals')): ?>
                      <div>
                        <?php if (can('update', 'deals')): ?>
                          <button class="btn btn-sm btn-outline-warning border-0 p-1 edit-activity-btn" data-bs-toggle="modal" data-bs-target="#editActivityModal" data-id="<?= $activity->activity_id ?>" data-name="<?= htmlspecialchars($activity->name) ?>" data-type="<?= htmlspecialchars($activity->type) ?>" data-start-date="<?= date('Y-m-d', strtotime($activity->start_time)) ?>" data-start-time="<?= date('H:i', strtotime($activity->start_time)) ?>" data-end-date="<?= $activity->end_time ? date('Y-m-d', strtotime($activity->end_time)) : '' ?>" data-end-time="<?= $activity->end_time ? date('H:i', strtotime($activity->end_time)) : '' ?>" data-description="<?= htmlspecialchars($activity->description) ?>" data-photo="<?= $activity->documentation_photo ? BASE_URL . '/uploads/activities/' . $activity->documentation_photo : '' ?>">
                            <i class="bi bi-pencil-fill"></i>
                          </button>
                        <?php endif; ?>
                        <?php if (can('delete', 'deals')): ?>
                          <form action="<?= BASE_URL; ?>/activities/delete/<?= $activity->activity_id; ?>" method="post" class="d-inline form-delete" data-item-name="<?= htmlspecialchars($activity->name); ?>">
                            <input type="hidden" name="redirect_url" value="<?= BASE_URL; ?>/deals/detail/<?= $data['deal']->deal_id; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1"><i class="bi bi-trash"></i></button>
                          </form>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                  <small class="text-muted d-block mb-2">Oleh: <?= htmlspecialchars($activity->owner_name); ?> pada <?= date('H:i', strtotime($activity->start_time)); ?></small>
                  <div class="p-3 bg-light rounded mt-2">
                    <?php if (!empty($activity->description)): ?><p class="mb-1 fst-italic">"<?= nl2br(htmlspecialchars($activity->description)); ?>"</p><?php endif; ?>
                    <?php if ($activity->documentation_photo): ?><?php if (!empty($activity->description)) echo '<hr>'; ?><a href="<?= BASE_URL; ?>/uploads/activities/<?= $activity->documentation_photo; ?>" target="_blank"><img src="<?= BASE_URL; ?>/uploads/activities/<?= $activity->documentation_photo; ?>" alt="Dokumentasi" class="img-fluid rounded mt-2" style="max-height: 150px;"></a><?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
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
      <form action="<?= BASE_URL; ?>/activities/add" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="related_item_id" value="<?= $data['deal']->deal_id; ?>">
          <input type="hidden" name="related_item_type" value="deal">
          <input type="hidden" name="redirect_url" value="<?= BASE_URL; ?>/deals/detail/<?= $data['deal']->deal_id; ?>">
          <div class="mb-3"><label class="form-label">Nama Aktivitas</label><input type="text" class="form-control" name="name" required></div>
          <div class="mb-3"><label class="form-label">Jenis</label><select name="type" class="form-select" required>
              <option value="Tugas">Tugas</option>
              <option value="Panggilan">Panggilan</option>
              <option value="Email">Email</option>
              <option value="Rapat">Rapat</option>
            </select></div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" class="form-control" name="start_date" value="<?= date('Y-m-d'); ?>" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Waktu Mulai</label><input type="time" class="form-control" name="start_time" value="<?= date('H:i'); ?>" required></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Selesai (Opsional)</label><input type="date" class="form-control" name="end_date"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Waktu Selesai (Opsional)</label><input type="time" class="form-control" name="end_time"></div>
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
          <input type="hidden" name="redirect_url" value="<?= BASE_URL; ?>/deals/detail/<?= $data['deal']->deal_id; ?>">
          <div class="mb-3"><label class="form-label">Nama Aktivitas</label><input type="text" id="edit_name" class="form-control" name="name" required></div>
          <div class="mb-3"><label class="form-label">Jenis</label><select id="edit_type" name="type" class="form-select" required>
              <option value="Tugas">Tugas</option>
              <option value="Panggilan">Panggilan</option>
              <option value="Email">Email</option>
              <option value="Rapat">Rapat</option>
            </select></div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Mulai</label><input type="date" id="edit_start_date" class="form-control" name="start_date" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Waktu Mulai</label><input type="time" id="edit_start_time" class="form-control" name="start_time" required></div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Tanggal Selesai (Opsional)</label><input type="date" id="edit_end_date" class="form-control" name="end_date"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Waktu Selesai (Opsional)</label><input type="time" id="edit_end_time" class="form-control" name="end_time"></div>
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
    document.querySelectorAll('.edit-activity-btn').forEach(button => {
      button.addEventListener('click', function() {
        // Ambil data dari tombol
        const id = this.dataset.id;
        const name = this.dataset.name;
        const type = this.dataset.type;
        const startDate = this.dataset.startDate;
        const startTime = this.dataset.startTime;
        const endDate = this.dataset.endDate;
        const endTime = this.dataset.endTime;
        const description = this.dataset.description;
        const photoUrl = this.dataset.photo;

        // Tentukan form dan elemen modal edit
        const form = document.getElementById('editActivityForm');
        form.action = '<?= BASE_URL ?>/activities/edit/' + id;

        document.getElementById('edit_name').value = name;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_start_date').value = startDate;
        document.getElementById('edit_start_time').value = startTime;
        document.getElementById('edit_end_date').value = endDate;
        document.getElementById('edit_end_time').value = endTime;
        document.getElementById('edit_description').value = description;

        // Tampilkan foto yang sudah ada
        const photoContainer = document.getElementById('current_photo_container');
        if (photoUrl) {
          photoContainer.innerHTML = `<p class="mb-1 small">Foto saat ini:</p><img src="${photoUrl}" style="max-height: 100px;" class="img-fluid rounded">`;
        } else {
          photoContainer.innerHTML = '';
        }
      });
    });
  });
</script>