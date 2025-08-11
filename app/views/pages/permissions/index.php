<!-- app/views/pages/permissions/index.php -->
<div class="container-fluid px-4">
  <h1 class="mt-4"><?= $data['title']; ?></h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active">Hak Akses</li>
  </ol>

  <?php flash('permission_message'); ?>

  <div class="card mb-4">
    <div class="card-header"><i class="bi bi-shield-lock-fill me-1"></i> Atur Hak Akses per Peran</div>
    <div class="card-body">
      <div class="col-md-4 mb-3">
        <label for="role_id_selector" class="form-label">Pilih Peran (Role)</label>
        <select id="role_id_selector" class="form-select">
          <?php foreach ($data['roles'] as $role): ?>
            <option value="<?= $role->role_id; ?>" <?= ($role->role_id == $data['selected_role_id']) ? 'selected' : ''; ?>>
              <?= $role->role_name; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <form action="<?= BASE_URL; ?>/permissions" method="POST">
        <input type="hidden" name="role_id" id="form_role_id" value="<?= $data['selected_role_id']; ?>">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Nama Menu</th>
                <th class="text-center">Lihat</th>
                <th class="text-center">Tambah</th>
                <th class="text-center">Edit</th>
                <th class="text-center">Hapus</th>
                <th class="text-center">Konversi</th>
              </tr>
            </thead>
            <tbody id="permissions-tbody">
              <?php foreach ($data['menus'] as $menu): ?>
                <tr>
                  <td><i class="bi <?= $menu->menu_icon; ?> me-2"></i><?= $menu->menu_name; ?></td>
                  <td class="text-center"><input class="form-check-input" type="checkbox" name="permissions[<?= $menu->menu_id ?>][read]" value="1"></td>
                  <td class="text-center"><input class="form-check-input" type="checkbox" name="permissions[<?= $menu->menu_id ?>][create]" value="1"></td>
                  <td class="text-center"><input class="form-check-input" type="checkbox" name="permissions[<?= $menu->menu_id ?>][update]" value="1"></td>
                  <td class="text-center"><input class="form-check-input" type="checkbox" name="permissions[<?= $menu->menu_id ?>][delete]" value="1"></td>
                  <td class="text-center">
                    <!-- Hanya tampilkan checkbox konversi untuk menu prospek -->
                    <?php if (strtolower($menu->menu_name) === 'prospek' || strtolower($menu->menu_name) === 'leads'): ?>
                      <input class="form-check-input" type="checkbox" name="permissions[<?= $menu->menu_id ?>][convert]" value="1">
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <button type="submit" class="btn btn-primary mt-3 float-end">Simpan Perubahan</button>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const roleSelector = document.getElementById('role_id_selector');
    if (roleSelector) {
      const formRoleIdInput = document.getElementById('form_role_id');
      const tbody = document.getElementById('permissions-tbody');

      const updateCheckboxes = (permissions) => {
        tbody.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
          const nameParts = checkbox.name.match(/\[(\d+)\]\[(\w+)\]/);
          if (!nameParts) return;
          const menuId = nameParts[1];
          const permType = nameParts[2];
          checkbox.checked = false;
          if (permissions[menuId] && permissions[menuId]['can_' + permType] == 1) {
            checkbox.checked = true;
          }
        });
      };

      const fetchPermissions = (roleId) => {
        fetch('<?= BASE_URL ?>/permissions/getByRole/' + roleId)
          .then(response => response.json())
          .then(data => updateCheckboxes(data));
      };

      roleSelector.addEventListener('change', function() {
        const roleId = this.value;
        window.history.pushState({}, '', '<?= BASE_URL ?>/permissions?role=' + roleId);
        formRoleIdInput.value = roleId;
        fetchPermissions(roleId);
      });

      fetchPermissions(roleSelector.value);
    }
  });
</script>