<!-- app/views/layouts/sidebar.php -->
<div id="sidebar-wrapper">
  <!-- Judul "Punyo CRM" dihapus dari sini -->
  <div class="list-group list-group-flush mt-4">
    <?php if (isset($_SESSION['allowed_menus'])): ?>
      <?php foreach ($_SESSION['allowed_menus'] as $menu): ?>
        <a href="<?= BASE_URL; ?>/<?= $menu->menu_url; ?>" class="list-group-item list-group-item-action <?= isActive($menu->menu_url); ?>" title="<?= $menu->menu_name; ?>">
          <i class="bi <?= $menu->menu_icon; ?>"></i>
          <span class="menu-text"><?= $menu->menu_name; ?></span>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>