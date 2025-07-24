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

  /* Efek hover pada baris tabel */
  .table-hover tbody tr {
    transition: background-color 0.2s ease-in-out;
  }

  .table-hover tbody tr:hover {
    background-color: #f8f9fa;
  }

  /* Styling tombol aksi */
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
</style>

<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Manajemen Kesepakatan</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item active">Kesepakatan</li>
    </ol>
  </div>

  <?php flash('deal_message'); ?>

  <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <form action="<?= BASE_URL; ?>/deals" method="GET" class="d-flex flex-wrap">
          <div class="me-2 mb-2">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="Cari kesepakatan..." value="<?= htmlspecialchars($data['search']); ?>">
              <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
          </div>
          <div class="me-2 mb-2">
            <select name="stage" class="form-select" onchange="this.form.submit()">
              <option value="">Semua Tahapan</option>
              <option value="Analisis Kebutuhan" <?= ($data['stage'] == 'Analisis Kebutuhan') ? 'selected' : ''; ?>>Analisis Kebutuhan</option>
              <option value="Proposal" <?= ($data['stage'] == 'Proposal') ? 'selected' : ''; ?>>Proposal</option>
              <option value="Negosiasi" <?= ($data['stage'] == 'Negosiasi') ? 'selected' : ''; ?>>Negosiasi</option>
              <option value="Menang" <?= ($data['stage'] == 'Menang') ? 'selected' : ''; ?>>Menang</option>
              <option value="Kalah" <?= ($data['stage'] == 'Kalah') ? 'selected' : ''; ?>>Kalah</option>
            </select>
          </div>
        </form>
        <div class="d-flex">
          <a href="<?= BASE_URL; ?>/deals/kanban" class="btn btn-secondary mb-2 me-2"><i class="bi bi-kanban-fill me-2"></i>Papan Kanban</a>
          <?php if (can('create', 'deals')): ?>
            <a href="<?= BASE_URL; ?>/deals/add" class="btn btn-primary mb-2"><i class="bi bi-plus-lg me-2"></i>Tambah Kesepakatan</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Narahubung</th>
              <th>Nama Instansi</th>
              <th>Tahapan</th>
              <th>Nilai (Rp)</th>
              <th>Pemilik</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($data['deals'])): ?>
              <tr>
                <td colspan="6" class="text-center py-5">Tidak ada data kesepakatan yang ditemukan.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($data['deals'] as $deal) : ?>
                <tr>
                  <td><strong><?= htmlspecialchars($deal->contact_name); ?></strong></td>
                  <td><?= htmlspecialchars($deal->company_name); ?></td>
                  <td>
                    <?php $stageClass = 'badge-stage-' . strtolower(str_replace(' ', '-', $deal->stage)); ?>
                    <span class="badge rounded-pill <?= $stageClass; ?>"><?= htmlspecialchars($deal->stage); ?></span>
                  </td>
                  <td><?= number_format($deal->value, 0, ',', '.'); ?></td>
                  <td><?= htmlspecialchars($deal->owner_name); ?></td>
                  <td class="text-center">
                    <a href="<?= BASE_URL; ?>/deals/detail/<?= $deal->deal_id; ?>" class="btn btn-info btn-sm text-white action-btn" title="Detail"><i class="bi bi-eye-fill"></i></a>
                    <?php if (can('update', 'deals')): ?>
                      <a href="<?= BASE_URL; ?>/deals/edit/<?= $deal->deal_id; ?>" class="btn btn-warning btn-sm text-white action-btn" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                    <?php endif; ?>
                    <?php if (can('delete', 'deals')): ?>
                      <form action="<?= BASE_URL; ?>/deals/delete/<?= $deal->deal_id; ?>" method="post" class="d-inline form-delete" data-item-name="<?= htmlspecialchars($deal->name); ?>">
                        <button type="submit" class="btn btn-danger btn-sm action-btn" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                      </form>
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
            <p class="text-muted">Menampilkan <?= count($data['deals']); ?> dari <?= $data['total_deals']; ?> data.</p>
          </div>
          <div class="col-md-6">
            <nav aria-label="Page navigation">
              <ul class="pagination justify-content-end">
                <?php if ($data['current_page'] > 1): ?>
                  <li class="page-item"><a class="page-link" href="?page=<?= $data['current_page'] - 1; ?>&<?= http_build_query(['search' => $data['search'], 'stage' => $data['stage']]); ?>">Previous</a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?>
                  <li class="page-item <?= ($i == $data['current_page']) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?= $i; ?>&<?= http_build_query(['search' => $data['search'], 'stage' => $data['stage']]); ?>"><?= $i; ?></a></li>
                <?php endfor; ?>
                <?php if ($data['current_page'] < $data['total_pages']): ?>
                  <li class="page-item"><a class="page-link" href="?page=<?= $data['current_page'] + 1; ?>&<?= http_build_query(['search' => $data['search'], 'stage' => $data['stage']]); ?>">Next</a></li>
                <?php endif; ?>
              </ul>
            </nav>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('.form-delete');
    deleteForms.forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const itemName = form.getAttribute('data-item-name');
        // Pastikan library SweetAlert2 sudah dimuat
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Anda akan menghapus kesepakatan "${itemName}". Tindakan ini tidak dapat dibatalkan!`,
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
        } else {
          // Fallback jika SweetAlert tidak ada
          if (confirm(`Apakah Anda yakin ingin menghapus "${itemName}"?`)) {
            form.submit();
          }
        }
      });
    });
  });
</script>