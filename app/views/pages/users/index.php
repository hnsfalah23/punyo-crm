<style>
  .table-hover tbody tr {
    transition: all 0.2s ease-in-out;
  }

  .table-hover tbody tr:hover {
    background-color: #f8f9fa;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
    z-index: 2;
    position: relative;
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
    <h1 class="mt-4">Manajemen Pengguna</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item active">Pengguna</li>
    </ol>
  </div>

  <?php flash('user_message'); ?>

  <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <form action="<?= BASE_URL; ?>/users" method="GET" class="d-flex flex-wrap">
          <div class="me-2 mb-2">
            <div class="input-group">
              <input type="text" name="search" class="form-control" placeholder="Cari pengguna..." value="">
              <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            </div>
          </div>
        </form>
        <?php if (can('create', 'Manajemen Pengguna')): ?>
          <a href="<?= BASE_URL; ?>/users/add" class="btn btn-primary mb-2"><i class="bi bi-plus-lg me-2"></i>Tambah Pengguna</a>
        <?php endif; ?>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Nama Pengguna</th>
              <th>Email</th>
              <th>Peran (Role)</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($data['users'])): ?>
              <tr>
                <td colspan="4" class="text-center py-5">Tidak ada data pengguna ditemukan.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($data['users'] as $user) : ?>
                <tr>
                  <td><strong><?= htmlspecialchars($user->name); ?></strong></td>
                  <td><?= htmlspecialchars($user->email); ?></td>
                  <td><span class="badge bg-success"><?= htmlspecialchars($user->role_name); ?></span></td>
                  <td class="text-center">
                    <?php if (can('update', 'Manajemen Pengguna')): ?>
                      <a href="<?= BASE_URL; ?>/users/edit/<?= $user->user_id; ?>" class="btn btn-warning btn-sm text-white action-btn" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                    <?php endif; ?>
                    <?php if (can('delete', 'Manajemen Pengguna')): ?>
                      <form action="<?= BASE_URL; ?>/users/delete/<?= $user->user_id; ?>" method="post" class="d-inline form-delete" data-item-name="<?= htmlspecialchars($user->name); ?>">
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
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Event listener untuk form hapus
    document.querySelectorAll('.form-delete').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const itemName = form.getAttribute('data-item-name');
        Swal.fire({
          title: 'Apakah Anda yakin?',
          text: `Anda akan menghapus "${itemName}".`,
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
      });
    });
  });
</script>