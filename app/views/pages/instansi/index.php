<style>
    /* Styling Umum */
    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
    }

    .table-custom {
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }

    .table-custom thead th {
        border: none;
        padding: 0.75rem 1.5rem;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        background-color: #f9fafb;
    }

    .table-custom tbody tr {
        background-color: #ffffff;
        transition: all 0.2s ease-in-out;
    }

    .instansi-name {
        font-weight: 600;
        color: #0d6efd;
        text-decoration: none;
    }

    .instansi-name:hover {
        text-decoration: underline;
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
    }

    .action-btn-sm {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
    }

    .details-link {
        cursor: pointer;
        color: #0d6efd;
        text-decoration: none;
    }

    .details-link:hover {
        text-decoration: underline;
    }

    .bg-purple {
        color: #fff;
        background-color: #8e44ad !important;
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

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <!-- Form Filter -->
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
                <!-- Tombol Aksi -->
                <div class="d-flex">
                    <button type="button" class="btn btn-primary mb-2 me-2" data-bs-toggle="modal" data-bs-target="#addInstansiModal"><i class="bi bi-plus-lg me-2"></i>Tambah Instansi</button>
                    <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#addContactModal"><i class="bi bi-person-plus-fill me-2"></i>Tambah Kontak</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Nama Instansi</th>
                            <th>Industri</th>
                            <th class="text-center">Total Kontak</th>
                            <th class="text-center">Total Peluang</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['instansi'])): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">Belum ada data instansi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['instansi'] as $item): ?>
                                <tr>
                                    <td>
                                        <a href="<?= BASE_URL; ?>/instansi/detail/<?= $item->company_id; ?>" class="instansi-name">
                                            <?= htmlspecialchars($item->name ?? ''); ?>
                                        </a>
                                        <p class="text-muted small mb-0"><?= htmlspecialchars($item->website ?? ''); ?></p>
                                    </td>
                                    <td><?= htmlspecialchars($item->industry ?? 'N/A'); ?></td>
                                    <td class="text-center">
                                        <a class="details-link" onclick="openDetailsModal(<?= $item->company_id; ?>, 'kontak', '<?= htmlspecialchars($item->name, ENT_QUOTES); ?>')">
                                            Lihat (<?= $item->total_contacts ?? 0; ?>)
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a class="details-link" onclick="openDetailsModal(<?= $item->company_id; ?>, 'peluang', '<?= htmlspecialchars($item->name, ENT_QUOTES); ?>')">
                                            Lihat (<?= $item->total_deals ?? 0; ?>)
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL; ?>/instansi/detail/<?= $item->company_id; ?>" class="btn btn-info btn-sm action-btn text-white" title="Lihat Detail"><i class="bi bi-eye-fill"></i></a>
                                        <?php if (can('update', 'instansi')): ?>
                                            <button type="button" class="btn btn-warning text-white btn-sm action-btn" title="Edit" onclick="openEditModal(<?= $item->company_id; ?>)"><i class="bi bi-pencil-fill"></i></button>
                                        <?php endif; ?>
                                        <?php if (can('delete', 'instansi')): ?>
                                            <button type="button" class="btn btn-danger btn-sm action-btn btn-delete" title="Hapus" data-id="<?= $item->company_id; ?>" data-name="<?= htmlspecialchars($item->name ?? ''); ?>"><i class="bi bi-trash-fill"></i></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Paginasi -->
            <?php if ($data['total_pages'] > 1): ?>
                <div class="row mt-3 align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">Menampilkan <?= count($data['instansi']); ?> dari <?= $data['total_instansi']; ?> data.</p>
                    </div>
                    <div class="col-md-6">
                        <nav>
                            <ul class="pagination justify-content-end mb-0">
                                <?php $queryParams = "&search=" . urlencode($data['search']) . "&filter_industry=" . urlencode($data['filter_industry']); ?>
                                <li class="page-item <?= $data['current_page'] <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $data['current_page'] - 1 . $queryParams ?>">Previous</a></li>
                                <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?>
                                    <li class="page-item <?= $i == $data['current_page'] ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i . $queryParams ?>"><?= $i; ?></a></li>
                                <?php endfor; ?>
                                <li class="page-item <?= $data['current_page'] >= $data['total_pages'] ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $data['current_page'] + 1 . $queryParams ?>">Next</a></li>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Instansi Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addInstansiForm">
                <div class="modal-body">
                    <div class="form-floating mb-3"><input type="text" name="name" class="form-control" placeholder="Nama Instansi" required><label>Nama Instansi</label></div>
                    <div class="form-floating mb-3"><input type="text" name="industry" class="form-control" placeholder="Industri"><label>Industri</label></div>
                    <div class="form-floating mb-3"><input type="url" name="website" class="form-control" placeholder="https://example.com"><label>Website</label></div>
                    <div class="form-floating mb-3"><textarea name="description" class="form-control" placeholder="Deskripsi" style="height: 100px"></textarea><label>Deskripsi</label></div>
                    <div class="mb-3">
                        <label for="add_gmaps_location" class="form-label">Lokasi Google Maps</label>
                        <input type="text" name="gmaps_location" id="add_gmaps_location" class="form-control" placeholder="Alamat atau link Google Maps" aria-describedby="gmapsHelp">
                        <div id="gmapsHelp" class="form-text">
                            Masukkan alamat lengkap atau salin tautan dari Google Maps.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Instansi -->
<div class="modal fade" id="editInstansiModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Instansi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editInstansiForm">
                <input type="hidden" name="company_id" id="edit_company_id">
                <div class="modal-body">
                    <div class="form-floating mb-3"><input type="text" name="name" id="edit_name" class="form-control" placeholder="Nama Instansi" required><label for="edit_name">Nama Instansi</label></div>
                    <div class="form-floating mb-3"><input type="text" name="industry" id="edit_industry" class="form-control" placeholder="Industri"><label for="edit_industry">Industri</label></div>
                    <div class="form-floating mb-3"><input type="url" name="website" id="edit_website" class="form-control" placeholder="https://example.com"><label for="edit_website">Website</label></div>
                    <div class="form-floating mb-3"><textarea name="description" id="edit_description" class="form-control" placeholder="Deskripsi" style="height: 100px"></textarea><label for="edit_description">Deskripsi</label></div>
                    <div class="mb-3">
                        <label for="edit_gmaps_location" class="form-label">Lokasi Google Maps</label>
                        <input type="text" name="gmaps_location" id="edit_gmaps_location" class="form-control" placeholder="Alamat atau link Google Maps" aria-describedby="gmapsHelpEdit">
                        <div id="gmapsHelpEdit" class="form-text">
                            Masukkan alamat lengkap atau salin tautan dari Google Maps.
                        </div>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kontak Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addContactForm">
                <div class="modal-body">
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

<!-- MODAL BARU: Untuk Detail Kontak & Peluang -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalTitle">Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailsModalContent" class="table-responsive">
                    <!-- Konten dinamis akan dimuat di sini -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BARU: Untuk Edit Kontak dari Halaman Index -->
<div class="modal fade" id="editContactFromIndexModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kontak</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editContactFromIndexForm">
                <input type="hidden" name="contact_id" id="edit_contact_id_index">
                <div class="modal-body">
                    <div class="form-floating mb-3"><input type="text" name="name" id="edit_contact_name_index" class="form-control" placeholder="Nama Kontak" required><label>Nama Kontak</label></div>
                    <div class="form-floating mb-3"><input type="email" name="email" id="edit_contact_email_index" class="form-control" placeholder="Email"><label>Email</label></div>
                    <div class="form-floating mb-3"><input type="tel" name="phone" id="edit_contact_phone_index" class="form-control" placeholder="Telepon"><label>Telepon</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener untuk form tambah instansi
        const addInstansiForm = document.getElementById('addInstansiForm');
        addInstansiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, '<?= BASE_URL; ?>/instansi/add', 'Instansi baru berhasil ditambahkan.');
        });

        // Event listener untuk form edit instansi
        const editInstansiForm = document.getElementById('editInstansiForm');
        editInstansiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, '<?= BASE_URL; ?>/instansi/edit', 'Data instansi berhasil diperbarui.');
        });

        // Event listener untuk form tambah kontak
        const addContactForm = document.getElementById('addContactForm');
        addContactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, '<?= BASE_URL; ?>/kontak/add', 'Kontak baru berhasil ditambahkan.');
        });

        // Event listener untuk tombol hapus instansi
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Anda akan menghapus instansi "${name}". Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`<?= BASE_URL; ?>/instansi/delete/${id}`, {
                                method: 'POST'
                            })
                            .then(response => response.json())
                            .then(res => handleAjaxResponse(res));
                    }
                });
            });
        });

        // Event delegation untuk tombol hapus kontak di dalam modal
        const detailsModalContent = document.getElementById('detailsModalContent');
        detailsModalContent.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-delete-contact') || e.target.closest('.btn-delete-contact')) {
                const button = e.target.closest('.btn-delete-contact');
                const id = button.dataset.id;
                const name = button.dataset.name;

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Anda akan menghapus kontak "${name}".`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`<?= BASE_URL; ?>/kontak/delete/${id}`, {
                                method: 'POST'
                            })
                            .then(response => response.json())
                            .then(res => handleAjaxResponse(res));
                    }
                });
            }
        });

        // Event listener untuk form edit kontak
        const editContactForm = document.getElementById('editContactFromIndexForm');
        editContactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit(this, '<?= BASE_URL; ?>/kontak/edit', 'Kontak berhasil diperbarui.');
        });
    });

    // Fungsi helper untuk menangani submit form AJAX
    async function handleFormSubmit(form, url, successMessage) {
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`;

        try {
            const formData = new FormData(form);
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            handleAjaxResponse(result, successMessage);
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tidak dapat terhubung ke server.'
            });
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    }

    // Fungsi helper untuk menangani respons AJAX dan notifikasi
    function handleAjaxResponse(result, successMessage = 'Aksi berhasil.') {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: successMessage,
                timer: 1500,
                showConfirmButton: false
            });
            setTimeout(() => location.reload(), 1500);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: result.message || 'Terjadi kesalahan.'
            });
        }
    }

    // Fungsi untuk membuka modal edit instansi
    async function openEditModal(id) {
        const form = document.getElementById('editInstansiForm');
        form.reset();
        const modal = new bootstrap.Modal(document.getElementById('editInstansiModal'));

        try {
            const response = await fetch(`<?= BASE_URL; ?>/instansi/getInstansiData/${id}`);
            const result = await response.json();
            if (result.success) {
                const data = result.data;
                document.getElementById('edit_company_id').value = data.company_id;
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_industry').value = data.industry || '';
                document.getElementById('edit_website').value = data.website || '';
                document.getElementById('edit_description').value = data.description || '';
                document.getElementById('edit_gmaps_location').value = data.gmaps_location || '';
                modal.show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: result.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tidak dapat mengambil data dari server.'
            });
        }
    }

    // Fungsi untuk membuka modal detail kontak/peluang
    async function openDetailsModal(id, type, instansiName) {
        const modalEl = document.getElementById('detailsModal');
        const modal = new bootstrap.Modal(modalEl);

        const titleEl = document.getElementById('detailsModalTitle');
        const contentEl = document.getElementById('detailsModalContent');

        const getStageBadgeClass = (stage) => {
            if (!stage) return 'bg-secondary';
            const lowerCaseStage = stage.toLowerCase();
            if (lowerCaseStage.includes('analisis')) return 'bg-primary';
            if (lowerCaseStage.includes('proposal')) return 'bg-purple';
            if (lowerCaseStage.includes('negosiasi')) return 'bg-warning text-dark';
            if (lowerCaseStage.includes('menang')) return 'bg-success';
            if (lowerCaseStage.includes('kalah')) return 'bg-danger';
            return 'bg-secondary';
        };

        const typeName = type === 'kontak' ? 'Kontak' : 'Peluang';
        titleEl.textContent = `Daftar ${typeName} untuk ${instansiName}`;
        contentEl.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>`;
        modal.show();

        try {
            const response = await fetch(`<?= BASE_URL; ?>/instansi/getRelatedData/${id}/${type}`);
            const result = await response.json();

            if (result.success && Array.isArray(result.data)) {
                let tableHTML = '<p>Tidak ada data ditemukan.</p>';
                if (result.data.length > 0) {
                    let headerHTML = '<tr>';
                    let bodyHTML = '';

                    if (type === 'kontak') {
                        headerHTML += '<th>Nama</th><th>Email</th><th>Telepon</th><th class="text-center">Aksi</th>';
                        result.data.forEach(item => {
                            bodyHTML += `<tr>
                            <td>${item.name || ''}</td>
                            <td>${item.email || ''}</td>
                            <td>${item.phone || ''}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm action-btn-sm text-white" title="Edit Kontak" onclick="openEditContactModal(${item.contact_id})"><i class="bi bi-pencil-fill"></i></button>
                                <button type="button" class="btn btn-danger btn-sm action-btn-sm btn-delete-contact" title="Hapus Kontak" data-id="${item.contact_id}" data-name="${item.name}"><i class="bi bi-trash-fill"></i></button>
                            </td>
                        </tr>`;
                        });
                    } else if (type === 'peluang') {
                        headerHTML += '<th>Nama Peluang</th><th>Tahap</th><th>Nilai</th>';
                        result.data.forEach(item => {
                            const badgeClass = getStageBadgeClass(item.stage);
                            bodyHTML += `<tr>
                            <td>${item.name || ''}</td>
                            <td><span class="badge ${badgeClass}">${item.stage || ''}</span></td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(item.value || 0)}</td>
                        </tr>`;
                        });
                    }
                    headerHTML += '</tr>';
                    tableHTML = `<table class="table table-striped"><thead>${headerHTML}</thead><tbody>${bodyHTML}</tbody></table>`;
                }
                contentEl.innerHTML = tableHTML;
            } else {
                contentEl.innerHTML = `<div class="alert alert-danger">${result.message || 'Gagal memuat data.'}</div>`;
            }
        } catch (error) {
            contentEl.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan saat menghubungi server.</div>`;
        }
    }

    // Fungsi untuk membuka modal edit kontak dari halaman index
    async function openEditContactModal(id) {
        const form = document.getElementById('editContactFromIndexForm');
        form.reset();

        const modalEl = document.getElementById('editContactFromIndexModal');
        const modal = new bootstrap.Modal(modalEl);

        try {
            const response = await fetch(`<?= BASE_URL; ?>/kontak/getKontakData/${id}`);
            const result = await response.json();

            if (result.success) {
                const data = result.data;
                document.getElementById('edit_contact_id_index').value = data.contact_id;
                document.getElementById('edit_contact_name_index').value = data.name;
                document.getElementById('edit_contact_email_index').value = data.email || '';
                document.getElementById('edit_contact_phone_index').value = data.phone || '';

                const detailsModal = bootstrap.Modal.getInstance(document.getElementById('detailsModal'));
                if (detailsModal) {
                    detailsModal.hide();
                }
                modal.show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: result.message
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tidak dapat mengambil data dari server.'
            });
        }
    }
</script>