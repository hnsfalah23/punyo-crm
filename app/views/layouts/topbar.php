<nav class="sb-topnav navbar navbar-expand navbar-light bg-white shadow-sm">
  <a class="navbar-brand ps-3 navbar-brand-gradient" href="<?= BASE_URL; ?>/dashboard">
    <i class="bi bi-person-rolodex"></i> Punyo CRM
  </a>

  <ul class="navbar-nav ms-auto me-3 me-lg-4">
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle d-flex align-items-center" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">

        <?php
        // Logika untuk menentukan foto profil
        $profilePicture = (!empty($_SESSION['user_photo']) && file_exists('uploads/profiles/' . $_SESSION['user_photo']))
          ? BASE_URL . '/uploads/profiles/' . $_SESSION['user_photo']
          : BASE_URL . '/assets/images/default.png';
        ?>
        <img src="<?= $profilePicture; ?>" class="rounded-circle me-2" width="30" height="30" alt="user" style="object-fit: cover;">

        <span><?= $_SESSION['user_name']; ?></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
        <li><a class="dropdown-item" href="<?= BASE_URL; ?>/users/profile">Profil</a></li>
        <li>
          <hr class="dropdown-divider" />
        </li>
        <li><a class="dropdown-item" href="<?= BASE_URL; ?>/auth/logout">Logout</a></li>
      </ul>
    </li>
  </ul>
</nav>