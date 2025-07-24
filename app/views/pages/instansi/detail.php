<style>
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

  .contact-item .contact-info {
    display: flex;
    flex-direction: column;
  }

  .contact-item .contact-actions {
    display: flex;
    align-items: center;
  }

  .contact-item .contact-links a {
    font-size: 1.2rem;
    text-decoration: none;
    color: #6c757d;
    transition: all 0.2s ease;
  }

  .contact-item .contact-links a:hover {
    transform: scale(1.2);
  }

  .contact-item .contact-links a.whatsapp:hover {
    color: #25D366;
  }

  .contact-item .contact-links a.email:hover {
    color: #0d6efd;
  }

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
    <h1 class="mt-4"><?= htmlspecialchars($data['instansi']->name ?? ''); ?></h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/instansi">Manajemen Instansi</a></li>
      <li class="breadcrumb-item active">Detail</li>
    </ol>
  </div>

  <?php flash('instansi_message'); ?>

  <div class="row">
    <div class="col-lg-5">
      <div class="card mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header"><i class="bi bi-info-circle-fill me-2"></i>Informasi Instansi</div>
        <div class="card-body">
          <div class="detail-item">
            <i class="bi bi-globe2"></i>
            <span class="detail-label">Website</span>
            <span class="detail-value"><a href="<?= htmlspecialchars($data['instansi']->website ?? ''); ?>" target="_blank"><?= htmlspecialchars($data['instansi']->website ?? ''); ?></a></span>
          </div>
          <div class="detail-item">
            <i class="bi bi-briefcase-fill"></i>
            <span class="detail-label">Industri</span>
            <span class="detail-value"><?= htmlspecialchars($data['instansi']->industry ?? ''); ?></span>
          </div>
          <hr>
          <p class="text-muted mb-2">Deskripsi:</p>
          <p><?= nl2br(htmlspecialchars($data['instansi']->description ?? '')); ?></p>
        </div>
      </div>
      <?php if (!empty($data['instansi']->gmaps_location)): ?>
        <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
          <div class="card-header"><i class="bi bi-geo-alt-fill me-2"></i>Lokasi</div>
          <div class="card-body p-0">
            <div class="ratio ratio-16x9">
              <iframe
                src="https://maps.google.com/maps?q=<?= urlencode($data['instansi']->gmaps_location) ?>&z=15&output=embed&iwloc=q"
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-lg-7">
      <div class="card mb-4" data-aos="fade-up" data-aos-delay="300">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="bi bi-people-fill me-2"></i>Kontak Terkait (<?= count($data['kontak']); ?>)</span>
          <?php if (can('create', 'instansi')): ?>
            <a href="<?= BASE_URL; ?>/kontak/add/<?= $data['instansi']->company_id; ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i> Tambah</a>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <?php if (!empty($data['kontak'])): ?>
              <?php foreach ($data['kontak'] as $kontak): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center contact-item">
                  <div class="contact-info">
                    <div class="fw-bold"><?= htmlspecialchars($kontak->name ?? ''); ?></div>
                    <div class="small text-muted"><?= htmlspecialchars($kontak->job_title ?? ''); ?></div>
                  </div>
                  <div class="contact-actions">
                    <div class="contact-links me-3">
                      <?php if (!empty($kontak->phone)): ?>
                        <a href="<?= format_wa_number($kontak->phone); ?>" target="_blank" class="whatsapp me-2" title="Kirim WhatsApp"><i class="bi bi-whatsapp"></i></a>
                      <?php endif; ?>
                      <?php if (!empty($kontak->email)): ?>
                        <a href="mailto:<?= htmlspecialchars($kontak->email ?? ''); ?>" class="email" title="Kirim Email"><i class="bi bi-envelope-fill"></i></a>
                      <?php endif; ?>
                    </div>
                    <div>
                      <?php if (can('update', 'instansi')): ?>
                        <a href="<?= BASE_URL; ?>/kontak/edit/<?= $kontak->contact_id; ?>" class="btn btn-warning btn-sm text-white action-btn" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                      <?php endif; ?>
                      <?php if (can('delete', 'instansi')): ?>
                        <form action="<?= BASE_URL; ?>/kontak/delete/<?= $kontak->contact_id; ?>" method="post" class="d-inline form-delete" data-item-name="<?= htmlspecialchars($kontak->name ?? ''); ?>">
                          <button type="submit" class="btn btn-danger btn-sm action-btn" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="list-group-item text-center text-muted">Belum ada kontak.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
      <div class="card mb-4" data-aos="fade-up" data-aos-delay="400">
        <div class="card-header"><i class="bi bi-briefcase-fill me-2"></i>Kesepakatan Terkait (<?= count($data['deals']); ?>)</div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <tbody>
                <?php if (!empty($data['deals'])): ?>
                  <?php foreach ($data['deals'] as $deal): ?>
                    <tr>
                      <td>
                        <a href="<?= BASE_URL; ?>/deals/detail/<?= $deal->deal_id; ?>" class="fw-bold text-decoration-none"><?= htmlspecialchars($deal->name); ?></a>
                        <div class="small text-muted">Pemilik: <?= htmlspecialchars($deal->owner_name); ?></div>
                        <?php if (!empty($deal->product_names)): ?>
                          <div class="product-list-small mt-1">
                            <i class="bi bi-box-seam"></i> <?= htmlspecialchars($deal->product_names); ?>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td class="text-end">
                        <?php
                        $stageClass = 'badge-stage-' . strtolower(str_replace(' ', '-', $deal->stage));
                        // Definisikan warna di sini jika belum ada di CSS utama
                        if (!function_exists('getStageBadgeClass')) {
                          function getStageBadgeClass($stage)
                          {
                            $map = [
                              'Kualifikasi' => 'badge-stage-kualifikasi',
                              'Analisis Kebutuhan' => 'badge-stage-analisis-kebutuhan',
                              'Proposal' => 'badge-stage-proposal',
                              'Negosiasi' => 'badge-stage-negosiasi',
                              'Menang' => 'badge-stage-menang',
                              'Kalah' => 'badge-stage-kalah'
                            ];
                            return $map[$stage] ?? 'bg-secondary';
                          }
                        }
                        ?>
                        <span class="badge rounded-pill <?= getStageBadgeClass($deal->stage); ?>"><?= htmlspecialchars($deal->stage); ?></span>
                        <div class="fw-bold">Rp <?= number_format($deal->value, 0, ',', '.'); ?></div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td class="text-center text-muted">Belum ada kesepakatan.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>