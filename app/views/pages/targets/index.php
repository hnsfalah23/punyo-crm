<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4"><?= $data['title']; ?></h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item active">Target Pengguna</li>
    </ol>
  </div>

  <?php flash('target_message'); ?>

  <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
      <i class="bi bi-bullseye me-1"></i>
      Atur Target Pengguna
    </div>
    <div class="card-body">
      <form action="<?= BASE_URL; ?>/targets" method="POST">
        <div class="row mb-3 align-items-end bg-light p-3 rounded">
          <div class="col-md-4">
            <label for="month-picker" class="form-label fw-bold">Pilih Bulan:</label>
            <input type="month" id="month-picker" class="form-control" name="month" value="<?= $data['selected_month']; ?>">
          </div>
          <div class="col-md-4">
            <label for="type-picker" class="form-label fw-bold">Jenis Target:</label>
            <select id="type-picker" name="type" class="form-select">
              <?php foreach ($data['target_types'] as $key => $type): ?>
                <option value="<?= $key; ?>" <?= ($data['selected_type'] == $key) ? 'selected' : ''; ?>>
                  <?= $type['name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Nama Pengguna</th>
                <th>Peran (Role)</th>
                <th>Target (<?= htmlspecialchars($data['target_types'][$data['selected_type']]['name']); ?>)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data['targetable_users'] as $user): ?>
                <tr>
                  <td><?= htmlspecialchars($user->name); ?></td>
                  <td><span class="badge bg-info text-dark"><?= htmlspecialchars($user->role_name); ?></span></td>
                  <td>
                    <div class="input-group">
                      <span class="input-group-text"><?= $data['selected_unit']; ?></span>
                      <input type="number" step="0.01" class="form-control" name="targets[<?= $user->user_id; ?>]" placeholder="0" value="<?= $data['targets'][$user->user_id] ?? ''; ?>"
                        <?= (can('create', 'targets') || can('update', 'targets')) ? '' : 'disabled' ?>>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php if (can('create', 'targets') || can('update', 'targets')): ?>
          <button type="submit" class="btn btn-primary float-end mt-3"><i class="bi bi-save me-2"></i>Simpan Semua Target</button>
        <?php endif; ?>
      </form>
    </div>
  </div>
</div>