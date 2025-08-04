<style>
    /* Styling Umum */
    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
    }

    /* Styling Tabel Modern */
    .table-custom {
        border-collapse: separate;
        border-spacing: 0 0.5rem;
        /* Memberi jarak antar baris */
    }

    .table-custom thead th {
        border: none;
        padding: 0.75rem 1.5rem;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        background-color: #f9fafb;
        /* Warna header tabel */
    }

    .table-custom thead th:first-child {
        border-top-left-radius: 0.5rem;
        border-bottom-left-radius: 0.5rem;
    }

    .table-custom thead th:last-child {
        border-top-right-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }

    .table-custom tbody tr {
        background-color: #ffffff;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border-radius: 0.5rem;
        /* Atur border radius untuk seluruh baris */
    }

    .table-custom tbody tr:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        z-index: 2;
        position: relative;
    }

    .table-custom tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border: none;
    }

    /* Styling Konten Tabel */
    .instansi-name {
        font-weight: 600;
        color: #1f2937;
        text-decoration: none;
    }

    .instansi-name:hover {
        color: #3b82f6;
    }

    .action-btn {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        text-decoration: none;
        margin: 2px 0;
        /* Memberi jarak vertikal */
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
</style>

<div class="container-fluid px-4">
    <div>
        <h1 class="mt-4">Manajemen Instansi</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Instansi</li>
        </ol>
    </div>

    <?php flash('instansi_message'); ?>
    <?php flash('kontak_message'); ?>

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <form action="<?= BASE_URL; ?>/instansi" method="GET" class="d-flex flex-wrap">
                    <div class="me-2 mb-2">
                        <div class="input-group"><input type="text" name="search" class="form-control" placeholder="Cari instansi..." value="<?= htmlspecialchars($data['search']); ?>"><button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button></div>
                    </div>
                    <div class="me-2 mb-2">
                        <select name="filter_industry" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Industri</option>
                            <?php foreach ($data['industries'] as $industry): ?>
                                <option value="<?= htmlspecialchars($industry->industry) ?>" <?= ($data['filter_industry'] == $industry->industry) ? 'selected' : '' ?>><?= htmlspecialchars($industry->industry) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                <div class="d-flex">
                    <button type="button" class="btn btn-primary mb-2 me-2" data-bs-toggle="modal" data-bs-target="#addInstansiModal"><i class="bi bi-building me-2"></i>Tambah Instansi</button>
                    <?php if (can('create', 'instansi')): ?>
                        <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#addContactModal"><i class="bi bi-person-plus-fill me-2"></i>Tambah Kontak</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Nama Instansi</th>
                            <th>Industri</th>
                            <th>Website</th>
                            <th>Deskripsi</th>
                            <th>Kontak Terkait</th>
                            <th>Peluang Terkait</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['instansi'])): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">Belum ada data instansi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['instansi'] as $item): ?>
                                <tr>
                                    <td>
                                        <a href="<?= BASE_URL; ?>/instansi/detail/<?= $item->company_id; ?>" class="instansi-name">
                                            <?= htmlspecialchars($item->name ?? ''); ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($item->industry ?? 'N/A'); ?></td>
                                    <td><a href="<?= htmlspecialchars($item->website ?? '#'); ?>" target="_blank" class="text-decoration-none"><?= htmlspecialchars($item->website ?? 'N/A'); ?></a></td>
                                    <td><?= htmlspecialchars(substr($item->description ?? '', 0, 50)); ?><?= strlen($item->description ?? '') > 50 ? '...' : '' ?></td>
                                    <td><?= htmlspecialchars($item->contact_names ?? 'Belum ada'); ?></td>
                                    <td><?= htmlspecialchars($item->deal_names ?? 'Belum ada'); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <a href="<?= BASE_URL; ?>/instansi/detail/<?= $item->company_id; ?>" class="btn btn-info text-white btn-sm action-btn" title="Detail"><i class="bi bi-eye-fill"></i></a>
                                            <?php if (can('update', 'instansi')): ?>
                                                <button type="button" class="btn btn-warning text-white btn-sm action-btn" title="Edit"
                                                    onclick="openEditModal(
                                                            '<?= $item->company_id; ?>', 
                                                            '<?= htmlspecialchars($item->name ?? '', ENT_QUOTES); ?>', 
                                                            '<?= htmlspecialchars($item->industry ?? '', ENT_QUOTES); ?>', 
                                                            '<?= htmlspecialchars($item->website ?? '', ENT_QUOTES); ?>', 
                                                            '<?= htmlspecialchars($item->description ?? '', ENT_QUOTES); ?>', 
                                                            '<?= htmlspecialchars($item->gmaps_location ?? '', ENT_QUOTES); ?>'
                                                        )">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if (can('delete', 'instansi')): ?>
                                                <form action="<?= BASE_URL; ?>/instansi/delete/<?= $item->company_id; ?>" method="post" class="d-inline form-delete" data-item-name="<?= htmlspecialchars($item->name ?? ''); ?>"><button type="submit" class="btn btn-danger btn-sm action-btn" title="Hapus"><i class="bi bi-trash-fill"></i></button></form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($data['total_pages'] > 1): ?>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p class="text-muted">Menampilkan <?= count($data['instansi']); ?> dari <?= $data['total_instansi']; ?> data.</p>
                    </div>
                    <div class="col-md-6">
                        <nav>
                            <ul class="pagination justify-content-end">
                                <?php if ($data['current_page'] > 1): ?><li class="page-item"><a class="page-link" href="?page=<?= $data['current_page'] - 1; ?>&search=<?= urlencode($data['search']) ?>&filter_industry=<?= urlencode($data['filter_industry']) ?>">Previous</a></li><?php endif; ?>
                                <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?><li class="page-item <?= ($i == $data['current_page']) ? 'active' : ''; ?>"><a class="page-link" href="?page=<?= $i; ?>&search=<?= urlencode($data['search']) ?>&filter_industry=<?= urlencode($data['filter_industry']) ?>"><?= $i; ?></a></li><?php endfor; ?>
                                <?php if ($data['current_page'] < $data['total_pages']): ?><li class="page-item"><a class="page-link" href="?page=<?= $data['current_page'] + 1; ?>&search=<?= urlencode($data['search']) ?>&filter_industry=<?= urlencode($data['filter_industry']) ?>">Next</a></li><?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Tambah Instansi -->
<div class="modal fade" id="addInstansiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Instansi Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL; ?>/instansi/add" method="POST">
                <div class="modal-body">
                    <div class="form-floating mb-3"><input type="text" name="name" class="form-control" placeholder="Nama Instansi" required><label>Nama Instansi</label></div>
                    <div class="form-floating mb-3"><input type="text" name="industry" class="form-control" placeholder="Industri"><label>Industri</label></div>
                    <div class="form-floating mb-3"><input type="url" name="website" class="form-control" placeholder="Website"><label>Website</label></div>
                    <div class="form-floating mb-3"><textarea name="description" class="form-control" placeholder="Deskripsi" style="height: 100px"></textarea><label>Deskripsi</label></div>
                    <div class="form-floating mb-3"><input type="text" name="gmaps_location" class="form-control" placeholder="Lokasi Google Maps"><label>Lokasi Google Maps</label></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Instansi -->
<div class="modal fade" id="editInstansiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Instansi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL; ?>/instansi/edit" method="POST" id="editInstansiForm">
                <input type="hidden" name="company_id" id="edit_company_id">
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" id="edit_name" class="form-control" placeholder="Nama Instansi" required>
                        <label>Nama Instansi</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="industry" id="edit_industry" class="form-control" placeholder="Industri">
                        <label>Industri</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="url" name="website" id="edit_website" class="form-control" placeholder="Website">
                        <label>Website</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea name="description" id="edit_description" class="form-control" placeholder="Deskripsi" style="height: 100px"></textarea>
                        <label>Deskripsi</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="gmaps_location" id="edit_gmaps_location" class="form-control" placeholder="Lokasi Google Maps">
                        <label>Lokasi Google Maps</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Kontak -->
<div class="modal fade" id="addContactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kontak Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL; ?>/kontak/add" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="redirect_url" value="<?= BASE_URL; ?>/instansi">
                    <div class="form-floating mb-3">
                        <select name="company_id" class="form-select" required>
                            <option value="" disabled selected>Pilih Instansi...</option>
                            <?php foreach ($data['all_instansi'] as $instansi): ?>
                                <option value="<?= $instansi->company_id ?>"><?= htmlspecialchars($instansi->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Instansi</label>
                    </div>
                    <div class="form-floating mb-3"><input type="text" name="name" class="form-control" placeholder="Nama Kontak" required><label>Nama Kontak</label></div>
                    <div class="form-floating mb-3"><input type="email" name="email" class="form-control" placeholder="Email"><label>Email</label></div>
                    <div class="form-floating mb-3"><input type="tel" name="phone" class="form-control" placeholder="Telepon"><label>Telepon</label></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function untuk membuka modal edit dengan data yang sudah terisi
    function openEditModal(id, name, industry, website, description, gmapsLocation) {
        document.getElementById('edit_company_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_industry').value = industry;
        document.getElementById('edit_website').value = website;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_gmaps_location').value = gmapsLocation;

        // Buka modal
        const editModal = new bootstrap.Modal(document.getElementById('editInstansiModal'));
        editModal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.form-delete');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const itemName = form.getAttribute('data-item-name');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Anda akan menghapus instansi "${itemName}". Tindakan ini tidak dapat dibatalkan!`,
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