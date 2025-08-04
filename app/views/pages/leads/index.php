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

  .action-btn {
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 2px;
    transition: all 0.2s ease;
  }

  .action-btn:hover {
    transform: translateY(-2px);
  }

  .contact-icon {
    font-size: 1.2rem;
    color: #6c757d;
  }

  .contact-icon:hover {
    color: #0d6efd;
  }

  .owner-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .owner-photo {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
  }
</style>

<div class="container-fluid px-4">
  <div>
    <h1 class="mt-4">Manajemen Prospek</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item active">Prospek</li>
    </ol>
  </div>

  <?php flash('lead_message'); ?>
  <?php flash('activity_message'); ?>

  <div class="card mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <form action="<?= BASE_URL; ?>/leads" method="GET" class="d-flex flex-wrap">
          <div class="me-2 mb-2">
            <div class="input-group"><input type="text" name="search" class="form-control" placeholder="Cari nama atau instansi..." value="<?= htmlspecialchars($data['search']); ?>"><button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button></div>
          </div>
          <div class="me-2 mb-2"><select name="status" class="form-select" onchange="this.form.submit()">
              <option value="">Semua Status</option>
              <option value="Baru" <?= ($data['status'] == 'Baru') ? 'selected' : ''; ?>>Baru</option>
              <option value="Koordinasi" <?= ($data['status'] == 'Koordinasi') ? 'selected' : ''; ?>>Koordinasi</option>
              <option value="Non Kualifikasi" <?= ($data['status'] == 'Non Kualifikasi') ? 'selected' : ''; ?>>Non Kualifikasi</option>
            </select></div>
        </form>
        <div class="d-flex">
          <button type="button" class="btn btn-secondary mb-2 me-2" data-bs-toggle="modal" data-bs-target="#addActivityModal"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Aktivitas</button>
          <?php if (can('create', 'leads')): ?>
            <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addLeadModal"><i class="bi bi-plus-lg me-2"></i>Tambah Prospek</button>
          <?php endif; ?>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Nama Prospek</th>
              <th>Nama Instansi</th>
              <th>Email</th>
              <th>Telepon</th>
              <th>Status</th>
              <th>Pemilik</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($data['leads'])): ?>
              <tr>
                <td colspan="7" class="text-center py-5">Tidak ada data prospek yang ditemukan.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($data['leads'] as $lead) : ?>
                <tr>
                  <td><a href="<?= BASE_URL; ?>/leads/detail/<?= $lead->lead_id; ?>" class="fw-bold text-decoration-none"><?= htmlspecialchars($lead->name); ?></a></td>
                  <td><?= htmlspecialchars($lead->company_name); ?></td>
                  <td class="text-center">
                    <?php if (!empty($lead->email)):
                      $subject = urlencode("Penawaran dari PT. Sriwijaya Internet Services");
                      $body = urlencode("Dengan hormat, Bapak/Ibu " . htmlspecialchars($lead->name) . ",\n\n[Isi email Anda di sini...]\n\nTerima kasih,\n" . $_SESSION['user_name']);
                      $gmail_url = "https://mail.google.com/mail/?view=cm&fs=1&to=" . htmlspecialchars($lead->email) . "&su=" . $subject . "&body=" . $body;
                    ?>
                      <a href="<?= $gmail_url; ?>" target="_blank" class="contact-icon" title="Kirim Email via Gmail">
                        <i class="bi bi-envelope-fill"></i>
                      </a>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <?php if (!empty($lead->phone)): ?>
                      <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $lead->phone); ?>" target="_blank" class="contact-icon" title="Hubungi via WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                      </a>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php $statusClass = 'badge-status-' . strtolower(str_replace(' ', '-', $lead->status)); ?>
                    <span class="badge rounded-pill px-3 py-2 <?= $statusClass; ?>"><?= htmlspecialchars($lead->status); ?></span>
                  </td>
                  <td>
                    <div class="owner-cell">
                      <?php
                      $ownerPhoto = (!empty($lead->owner_photo) && file_exists('uploads/profiles/' . $lead->owner_photo))
                        ? BASE_URL . '/uploads/profiles/' . $lead->owner_photo
                        : BASE_URL . '/assets/images/default.png';
                      ?>
                      <img src="<?= $ownerPhoto; ?>" alt="<?= htmlspecialchars($lead->owner_name); ?>" class="owner-photo">
                      <span><?= htmlspecialchars($lead->owner_name); ?></span>
                    </div>
                  </td>
                  <td class="text-center">
                    <?php if ($lead->status != 'Kualifikasi' && can('create', 'deals')): ?>
                      <button class="btn btn-success btn-sm action-btn quick-convert-btn" title="Konversi" data-id="<?= $lead->lead_id; ?>" data-name="<?= htmlspecialchars($lead->name); ?>"><i class="bi bi-arrow-repeat"></i></button>
                    <?php endif; ?>
                    <a href="<?= BASE_URL; ?>/leads/detail/<?= $lead->lead_id; ?>" class="btn btn-info btn-sm text-white action-btn" title="Detail"><i class="bi bi-eye-fill"></i></a>
                    <?php if (can('update', 'leads')): ?>
                      <button type="button" class="btn btn-warning btn-sm text-white action-btn edit-lead-btn" title="Edit" data-id="<?= $lead->lead_id; ?>"><i class="bi bi-pencil-fill"></i></button>
                    <?php endif; ?>
                    <?php if (can('delete', 'leads')): ?>
                      <form action="<?= BASE_URL; ?>/leads/delete/<?= $lead->lead_id; ?>" method="post" class="d-inline form-delete" data-item-name="<?= htmlspecialchars($lead->name); ?>"><button type="submit" class="btn btn-danger btn-sm action-btn" title="Hapus"><i class="bi bi-trash-fill"></i></button></form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($data['total_pages'] > 1): ?>
        <div class="row mt-3">
          <div class="col-md-6">
            <p class="text-muted">Menampilkan <?= count($data['leads']); ?> dari <?= $data['total_leads']; ?> data.</p>
          </div>
          <div class="col-md-6">
            <nav>
              <ul class="pagination justify-content-end">
                <?php if ($data['current_page'] > 1): ?><li class="page-item"><a class="page-link" href="?page=<?= $data['current_page'] - 1; ?>&<?= http_build_query(['search' => $data['search'], 'status' => $data['status']]); ?>">Previous</a></li><?php endif; ?>
                <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?><li class="page-item <?= ($i == $data['current_page']) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?= $i; ?>&<?= http_build_query(['search' => $data['search'], 'status' => $data['status']]); ?>"><?= $i; ?></a></li><?php endfor; ?>
                <?php if ($data['current_page'] < $data['total_pages']): ?><li class="page-item"><a class="page-link" href="?page=<?= $data['current_page'] + 1; ?>&<?= http_build_query(['search' => $data['search'], 'status' => $data['status']]); ?>">Next</a></li><?php endif; ?>
              </ul>
            </nav>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="modal fade" id="addLeadModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Prospek Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL; ?>/leads/add" method="POST" id="addLeadForm">
        <div class="modal-body">
          <div class="form-floating mb-3"><input type="text" name="name" class="form-control" placeholder="Nama Prospek" required><label>Nama Prospek</label></div>
          <div class="form-floating mb-3"><input type="text" name="company_name" class="form-control" placeholder="Nama Instansi"><label>Nama Instansi</label></div>
          <div class="form-floating mb-3"><input type="email" name="email" class="form-control" placeholder="Email"><label>Email</label></div>
          <div class="form-floating mb-3"><input type="tel" name="phone" class="form-control" placeholder="Telepon"><label>Telepon</label></div>
          <div class="form-floating mb-3"><select name="status" id="add_status" class="form-select">
              <option value="Baru" selected>Baru</option>
              <option value="Koordinasi">Koordinasi</option>
              <option value="Kualifikasi">Kualifikasi</option>
              <option value="Non Kualifikasi">Non Kualifikasi</option>
            </select><label>Status</label></div>
          <div class="form-floating mb-3"><input type="text" name="source" class="form-control" placeholder="Sumber Prospek"><label>Sumber Prospek</label></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editLeadModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Prospek</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editLeadForm" method="POST">
        <div class="modal-body">
          <input type="hidden" name="redirect_url" value="<?= BASE_URL; ?>/leads">
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

<div class="modal fade" id="addActivityModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Aktivitas Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL; ?>/activities/add" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Terkait Prospek</label><select name="related_item_id" class="form-select" required>
              <option value="" disabled selected>Pilih Prospek...</option><?php foreach ($data['all_leads_for_activity'] as $lead): ?><option value="<?= $lead->lead_id ?>"><?= htmlspecialchars($lead->name) ?> (<?= htmlspecialchars($lead->company_name) ?>)</option><?php endforeach; ?>
            </select></div>
          <input type="hidden" name="related_item_type" value="lead"><input type="hidden" name="redirect_url" value="<?= BASE_URL; ?>/leads">
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

<form id="quick-convert-form" method="post" class="d-none"></form>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.quick-convert-btn').forEach(button => {
      button.addEventListener('click', function() {
        const leadId = this.dataset.id;
        const leadName = this.dataset.name;
        const form = document.getElementById('quick-convert-form');

        Swal.fire({
          title: 'Apakah anda yakin ingin mengkonversi prospek ini?',
          html: `Tindakan ini akan mengubah prospek menjadi <strong>Peluang</strong> dan <strong>Instansi</strong> baru dalam sistem. Pastikan semua informasi telah diperiksa sebelum melanjutkan.`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yakin',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#6c757d',
        }).then((result) => {
          if (result.isConfirmed) {
            form.action = `<?= BASE_URL; ?>/leads/quickConvert/${leadId}`;
            form.submit();
          }
        });
      });
    });

    // 2. Script untuk Modal Edit
    const editModal = new bootstrap.Modal(document.getElementById('editLeadModal'));
    const editForm = document.getElementById('editLeadForm');
    const editStatusSelect = document.getElementById('edit_status');
    let originalStatus = '';
    document.querySelectorAll('.edit-lead-btn').forEach(button => {
      button.addEventListener('click', function() {
        const leadId = this.dataset.id;
        fetch(`<?= BASE_URL ?>/leads/getLeadJson/${leadId}`)
          .then(response => response.json())
          .then(data => {
            if (data) {
              editForm.action = `<?= BASE_URL ?>/leads/edit/${leadId}`;
              document.getElementById('edit_name').value = data.name;
              document.getElementById('edit_company_name').value = data.company_name;
              document.getElementById('edit_email').value = data.email;
              document.getElementById('edit_phone').value = data.phone;
              editStatusSelect.value = data.status;
              document.getElementById('edit_source').value = data.source;
              originalStatus = data.status;
              editModal.show();
            }
          });
      });
    });
    editForm.addEventListener('submit', function(e) {
      if (editStatusSelect.value === 'Kualifikasi' && originalStatus !== 'Kualifikasi') {
        e.preventDefault();
        Swal.fire({
          title: 'Konversi Prospek?',
          html: "Status diubah menjadi <strong>Terkualifikasi</strong>. <br>Prospek ini akan dikonversi menjadi <strong>Instansi</strong>, <strong>Kontak</strong>, dan <strong>Peluang</strong> baru.",
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yakin & Konversi',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#28a745',
        }).then((result) => {
          if (result.isConfirmed) {
            editForm.submit();
          }
        });
      }
    });

    // 3. Script untuk Modal Tambah Prospek
    const addLeadForm = document.getElementById('addLeadForm');
    const addStatusSelect = document.getElementById('add_status');
    addLeadForm.addEventListener('submit', function(e) {
      if (addStatusSelect.value === 'Kualifikasi') {
        e.preventDefault();
        Swal.fire({
          title: 'Konversi Prospek?',
          html: "Status diatur menjadi <strong>Terkualifikasi</strong>. <br>Prospek ini akan langsung dikonversi menjadi <strong>Instansi</strong>, <strong>Kontak</strong>, dan <strong>Peluang</strong> baru setelah disimpan.",
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yakin & Konversi',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#28a745',
        }).then((result) => {
          if (result.isConfirmed) {
            addLeadForm.submit();
          }
        });
      }
    });

    // 4. Script untuk Konfirmasi Hapus
    document.querySelectorAll('.form-delete').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const itemName = form.getAttribute('data-item-name');
        Swal.fire({
          title: 'Apakah Anda yakin?',
          text: `Anda akan menghapus "${itemName}". Tindakan ini tidak dapat dibatalkan!`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    });
  });
</script>