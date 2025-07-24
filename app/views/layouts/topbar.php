<!-- app/views/layouts/topbar.php -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <div class="container-fluid">
    <!-- Diperbarui: Ikon diganti dan styling hover ditambahkan -->
    <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL; ?>/dashboard">
      <i class="bi bi-person-rolodex fs-4 me-2 text-primary"></i>
      <span class="fw-bolder fs-4 navbar-brand-gradient">Punyo CRM</span>
    </a>

    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <?= htmlspecialchars($_SESSION['user_name']); ?>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
          <li><a class="dropdown-item" href="#">Profil</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li><a class="dropdown-item" href="<?= BASE_URL; ?>/auth/logout">Logout</a></li>
        </ul>
      </li>
    </ul>
  </div>
</nav>