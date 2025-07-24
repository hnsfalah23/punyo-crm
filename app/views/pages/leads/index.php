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

  /* Efek hover pada baris tabel */
  .table-hover tbody tr {
    transition: all 0.2s ease-in-out;
  }

  .table-hover tbody tr:hover {
    background-color: #f8f9fa;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
    z-index: 2;
    position: relative;
  }

  /* Styling tombol aksi */
  .action-btn {
    width: 35px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    text-decoration: none;
    margin: 0 2px;
    transition: all 0.2s ease;
  }

  .action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  }
</style>

<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Manajemen Prospek</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item active">Prospek</li>
    </ol>
  </div>

  <?php flash('lead_message'); ?>

  <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <form action="<?= BASE_URL; ?>/leads" method="GET" class="d-flex flex-wrap">
          <div class="me-2 mb-2">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="Cari nama atau instansi..." value="<?= htmlspecialchars($data['search']); ?>">
              <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
          </div>
          <div class="me-2 mb-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
              <option value="">Semua Status</option>
              <option value="Baru" <?= ($data['status'] == 'Baru') ? 'selected' : ''; ?>>Baru</option>
              <option value="Koordinasi" <?= ($data['status'] == 'Koordinasi') ? 'selected' : ''; ?>>Koordinasi</option>
              <option value="Kualifikasi" <?= ($data['status'] == 'Kualifikasi') ? 'selected' : ''; ?>>Kualifikasi</option>
              <option value="Non Kualifikasi" <?= ($data['status'] == 'Non Kualifikasi') ? 'selected' : ''; ?>>Non Kualifikasi</option>
            </select>
          </div>
        </form>
        <?php if (can('create', 'leads')): ?>
          <a href="<?= BASE_URL; ?>/leads/add" class="btn btn-primary mb-2"><i class="bi bi-plus-lg me-2"></i>Tambah Prospek</a>
        <?php endif; ?>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Nama Prospek</th>
              <th>Nama Instansi</th>
              <th>Status</th>
              <th>Pemilik</th>
              <th>Sumber</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($data['leads'])): ?>
              <tr>
                <td colspan="6" class="text-center py-5">Tidak ada data prospek yang ditemukan.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($data['leads'] as $lead) : ?>
                <tr>
                  <td><a href="<?= BASE_URL ?>/leads/detail/<?= $lead->lead_id ?>" class="fw-bold text-decoration-none"><?= htmlspecialchars($lead->name); ?></a></td>
                  <td><?= htmlspecialchars($lead->company_name); ?></td>
                  <td>
                    <?php $statusClass = 'badge-status-' . strtolower(str_replace(' ', '-', $lead->status)); ?>
                    <span class="badge rounded-pill px-3 py-2 <?= $statusClass; ?>"><?= htmlspecialchars($lead->status); ?></span>
                  </td>
                  <td><?= htmlspecialchars($lead->owner_name); ?></td>
                  <td><?= htmlspecialchars($lead->source); ?></td>
                  <td class="text-center">
                    <?php if ($lead->status == 'Kualifikasi'): ?>
                      <?php if (can('create', 'deals')): ?>
                        <a href="<?= BASE_URL; ?>/leads/convert/<?= $lead->lead_id; ?>" class="btn btn-success btn-sm action-btn" title="Konversi"><i class="bi bi-check2-circle"></i></a>
                      <?php endif; ?>
                    <?php else: ?>
                      <?php if (can('update', 'leads')): ?>
                        <a href="<?= BASE_URL; ?>/leads/edit/<?= $lead->lead_id; ?>" class="btn btn-warning btn-sm text-white action-btn" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                      <?php endif; ?>
                      <?php if (can('delete', 'leads')): ?>
                        <form action="<?= BASE_URL; ?>/leads/delete/<?= $lead->lead_id; ?>" method="post" class="d-inline form-delete" data-item-name="<?= htmlspecialchars($lead->name); ?>">
                          <button type="submit" class="btn btn-danger btn-sm action-btn" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                        </form>
                      <?php endif; ?>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="row mt-3">
        <div class="col-md-6">
          <p class="text-muted">Menampilkan <?= count($data['leads']); ?> dari <?= $data['total_leads']; ?> data.</p>
        </div>
        <div class="col-md-6">
          <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end">
              <?php if ($data['current_page'] > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $data['current_page'] - 1; ?>&<?= http_build_query(['search' => $data['search'], 'status' => $data['status'], 'limit' => $data['limit']]); ?>">Previous</a></li>
              <?php endif; ?>
              <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?>
                <li class="page-item <?= ($i == $data['current_page']) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?= $i; ?>&<?= http_build_query(['search' => $data['search'], 'status' => $data['status'], 'limit' => $data['limit']]); ?>"><?= $i; ?></a></li>
              <?php endfor; ?>
              <?php if ($data['current_page'] < $data['total_pages']): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $data['current_page'] + 1; ?>&<?= http_build_query(['search' => $data['search'], 'status' => $data['status'], 'limit' => $data['limit']]); ?>">Next</a></li>
              <?php endif; ?>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>