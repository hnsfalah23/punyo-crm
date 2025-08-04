<div class="container-fluid px-4">
  <h1 class="mt-4">Semua Aktivitas</h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Aktivitas</li>
  </ol>

  <div class="card mb-4">
    <div class="card-header">
      <i class="bi bi-clock-history me-1"></i>
      Histori Aktivitas
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>Nama Aktivitas</th>
              <th>Terkait</th>
              <th>Jenis</th>
              <th>Waktu Mulai</th>
              <th>Oleh</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($data['activities'])): ?>
              <tr>
                <td colspan="5" class="text-center">Belum ada aktivitas.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($data['activities'] as $activity): ?>
                <tr>
                  <td><?= htmlspecialchars($activity->name) ?></td>
                  <td>
                    <?php if (!empty($activity->related_item_name)): ?>
                      <a href="<?= BASE_URL ?>/<?= $activity->related_item_link ?>">
                        <?= htmlspecialchars($activity->related_item_name) ?>
                      </a>
                      <span class="badge bg-secondary"><?= ucfirst($activity->related_item_type == 'deal' ? 'Peluang' : 'Prospek') ?></span>
                    <?php else: ?>
                      N/A
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($activity->type) ?></td>
                  <td><?= date('d M Y, H:i', strtotime($activity->start_time)) ?></td>
                  <td><?= htmlspecialchars($activity->owner_name) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>