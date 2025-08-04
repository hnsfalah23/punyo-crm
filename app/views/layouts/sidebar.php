<div id="sidebar-wrapper">
  <div class="sidebar-heading">
    <i class="bi bi-person-rolodex"></i>
    <span class="sidebar-brand-text">Punyo CRM</span>
  </div>
  <ul class="list-group list-group-flush">
    <?php if (can('read', 'dashboard')) : ?>
      <li><a href="<?= BASE_URL; ?>/dashboard" class="list-group-item list-group-item-action <?= isActive('dashboard'); ?>" title="Dashboard"><i class="bi bi-speedometer2"></i><span class="menu-text">Dashboard</span></a></li>
    <?php endif; ?>

    <?php if (can('read', 'deals')) : ?>
      <li><a href="<?= BASE_URL; ?>/deals/kanban" class="list-group-item list-group-item-action <?= isActive('deals/kanban') ? 'active' : ''; ?>" title="Kanban Peluang"><i class="bi bi-kanban-fill"></i><span class="menu-text">Kanban Peluang</span></a></li>
    <?php endif; ?>

    <?php if (can('read', 'leads')) : ?>
      <li><a href="<?= BASE_URL; ?>/leads" class="list-group-item list-group-item-action <?= isActive('leads'); ?>" title="Prospek"><i class="bi bi-person-lines-fill"></i><span class="menu-text">Prospek</span></a></li>
    <?php endif; ?>

    <?php if (can('read', 'deals')) : ?>
      <li><a href="<?= BASE_URL; ?>/deals" class="list-group-item list-group-item-action <?= (isActive('deals') && !isActive('deals/kanban')) ? 'active' : ''; ?>" title="Peluang"><i class="bi bi-briefcase-fill"></i><span class="menu-text">Peluang</span></a></li>
    <?php endif; ?>

    <?php if (can('read', 'instansi')) : ?>
      <li><a href="<?= BASE_URL; ?>/instansi" class="list-group-item list-group-item-action <?= isActive('instansi'); ?>" title="Instansi"><i class="bi bi-building"></i><span class="menu-text">Instansi</span></a></li>
    <?php endif; ?>

    <?php if (can('read', 'activities')) : ?>
      <li><a href="<?= BASE_URL; ?>/activities" class="list-group-item list-group-item-action <?= isActive('activities'); ?>" title="Aktivitas"><i class="bi bi-clock-history"></i><span class="menu-text">Aktivitas</span></a></li>
    <?php endif; ?>

    <?php if (can('read', 'reports')) : ?>
      <li><a href="<?= BASE_URL; ?>/reports" class="list-group-item list-group-item-action <?= isActive('reports'); ?>" title="Laporan"><i class="bi bi-bar-chart-line-fill"></i><span class="menu-text">Laporan</span></a></li>
    <?php endif; ?>
    <?php if (can('read', 'targets')) : ?>
      <li><a href="<?= BASE_URL; ?>/targets" class="list-group-item list-group-item-action <?= isActive('targets'); ?>" title="Target"><i class="bi bi-bullseye"></i><span class="menu-text">Target</span></a></li>
    <?php endif; ?>

    <?php if (can('read', 'users') || can('read', 'products') || can('read', 'permissions')) : ?>
      <li class="has-dropdown <?= (isActive('users') || isActive('products') || isActive('permissions')) ? 'open' : ''; ?>">
        <a href="#" class="list-group-item list-group-item-action" title="Pengaturan"><i class="bi bi-gear-fill"></i><span class="menu-text">Pengaturan</span><i class="bi bi-chevron-right dropdown-arrow"></i></a>
        <ul class="submenu">
          <?php if (can('read', 'products')) : ?>
            <li><a href="<?= BASE_URL; ?>/products" class="<?= isActive('products') ? 'active' : ''; ?>">Manajemen Produk</a></li>
          <?php endif; ?>
          <?php if (can('read', 'users')) : ?>
            <li><a href="<?= BASE_URL; ?>/users" class="<?= isActive('users') ? 'active' : ''; ?>">Manajemen Pengguna</a></li>
          <?php endif; ?>
          <?php if (can('read', 'permissions')) : ?>
            <li><a href="<?= BASE_URL; ?>/permissions" class="<?= isActive('permissions') ? 'active' : ''; ?>">Hak Akses</a></li>
          <?php endif; ?>
        </ul>
      </li>
    <?php endif; ?>
  </ul>
</div>