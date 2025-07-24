<style>
    .table-hover tbody tr {
        transition: all 0.2s ease-in-out;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        /* transform: scale(1.015); */
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
        <h1 class="mt-4">Manajemen Instansi</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
            <li class="breadcrumb-item active">Instansi</li>
        </ol>
    </div>

    <?php flash('instansi_message'); ?>

    <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <form action="<?= BASE_URL; ?>/instansi" method="GET" class="d-flex flex-wrap">
                    <div class="me-2 mb-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama instansi..." value="">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </form>
                <?php if (can('create', 'instansi')): ?>
                    <a href="<?= BASE_URL; ?>/instansi/add" class="btn btn-primary mb-2"><i class="bi bi-plus-lg me-2"></i>Tambah Instansi</a>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Instansi</th>
                            <th>Website</th>
                            <th>Industri</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['instansi'])): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">Tidak ada data instansi ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data['instansi'] as $item) : ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($item->name ?? ''); ?></strong></td>
                                    <td><a href="<?= htmlspecialchars($item->website ?? ''); ?>" target="_blank"><?= htmlspecialchars($item->website ?? ''); ?></a></td>
                                    <td><?= htmlspecialchars($item->industry ?? ''); ?></td>
                                    <td class="text-center">
                                        <a href="<?= BASE_URL; ?>/instansi/detail/<?= $item->company_id; ?>" class="btn btn-info btn-sm text-white action-btn" title="Detail"><i class="bi bi-eye-fill"></i></a>
                                        <?php if (can('update', 'instansi')): ?>
                                            <a href="<?= BASE_URL; ?>/instansi/edit/<?= $item->company_id; ?>" class="btn btn-warning btn-sm text-white action-btn" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                        <?php endif; ?>
                                        <?php if (can('delete', 'instansi')): ?>
                                            <form action="<?= BASE_URL; ?>/instansi/delete/<?= $item->company_id; ?>" method="post" class="d-inline form-delete" data-item-name="<?= htmlspecialchars($item->name ?? ''); ?>">
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