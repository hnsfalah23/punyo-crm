<div class="container-fluid px-4">
  <h1 class="mt-4">Edit Kesepakatan</h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/deals">Kesepakatan</a></li>
    <li class="breadcrumb-item active">Edit</li>
  </ol>

  <?php flash('deal_message'); ?>

  <div class="row">
    <div class="col-lg-8">
      <form action="<?= BASE_URL; ?>/deals/edit/<?= $data['id']; ?>" method="POST" id="deal-form">
        <div class="card mb-4">
          <div class="card-header"><i class="bi bi-info-circle-fill me-2"></i>Detail Utama</div>
          <div class="card-body">
            <div class="form-floating mb-3">
              <input type="text" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" id="name" name="name" placeholder="Nama Kesepakatan" value="<?= htmlspecialchars($data['name']); ?>" required>
              <label for="name">Nama Kesepakatan</label>
              <span class="invalid-feedback"><?= $data['name_err']; ?></span>
            </div>
            <div class="form-floating mb-3">
              <select name="contact_id" id="contact_id" class="form-select <?= (!empty($data['contact_id_err'])) ? 'is-invalid' : ''; ?>" required>
                <option value="" disabled>Pilih Kontak...</option>
                <?php foreach ($data['contacts'] as $contact): ?>
                  <option value="<?= $contact->contact_id; ?>" <?= ($data['contact_id'] == $contact->contact_id) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($contact->name); ?> (<?= htmlspecialchars($contact->company_name); ?>)
                  </option>
                <?php endforeach; ?>
              </select>
              <label for="contact_id">Kontak Terkait</label>
              <span class="invalid-feedback"><?= $data['contact_id_err']; ?></span>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-floating mb-3">
                  <select name="stage" id="stage" class="form-select">
                    <option value="Analisis Kebutuhan" <?= ($data['stage'] == 'Analisis Kebutuhan') ? 'selected' : ''; ?>>Analisis Kebutuhan</option>
                    <option value="Proposal" <?= ($data['stage'] == 'Proposal') ? 'selected' : ''; ?>>Proposal</option>
                    <option value="Negosiasi" <?= ($data['stage'] == 'Negosiasi') ? 'selected' : ''; ?>>Negosiasi</option>
                    <option value="Menang" <?= ($data['stage'] == 'Menang') ? 'selected' : ''; ?>>Menang</option>
                    <option value="Kalah" <?= ($data['stage'] == 'Kalah') ? 'selected' : ''; ?>>Kalah</option>
                  </select>
                  <label for="stage">Tahapan</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating mb-3">
                  <input type="date" class="form-control" id="expected_close_date" name="expected_close_date" value="<?= $data['expected_close_date']; ?>">
                  <label for="expected_close_date">Perkiraan Pembayaran</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php if ($data['stage'] == 'Analisis Kebutuhan'): ?>
          <div class="card mb-4">
            <div class="card-header bg-info text-white"><i class="bi bi-list-check me-2"></i>Catatan Kebutuhan Proyek</div>
            <div class="card-body">
              <div class="form-floating">
                <textarea class="form-control" placeholder="Catat kebutuhan proyek di sini" id="requirements_notes" name="requirements_notes" style="height: 150px"><?= htmlspecialchars($data['requirements_notes'] ?? ''); ?></textarea>
                <label for="requirements_notes">Tuliskan detail kebutuhan, spesifikasi, dll.</label>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </form>

      <?php if ($data['stage'] == 'Proposal' || $data['stage'] == 'Negosiasi'): ?>
        <form action="<?= BASE_URL; ?>/deals/updateProposal/<?= $data['id']; ?>" method="POST">
          <div class="card mb-4" id="proposal-section">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
              <span><i class="bi bi-file-earmark-text-fill me-2"></i>Buat Proposal Penawaran</span>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Nomor Surat</label><input type="text" class="form-control" name="proposal[proposal_number]" value="<?= htmlspecialchars($data['proposal']->proposal_number ?? '001/MKT-SIS/VII/2025'); ?>"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Lampiran</label><input type="text" class="form-control" name="proposal[attachment]" value="<?= htmlspecialchars($data['proposal']->attachment ?? '1 Berkas'); ?>"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Perihal</label><input type="text" class="form-control" name="proposal[subject]" value="<?= htmlspecialchars($data['proposal']->subject ?? 'Penawaran Harga Instalasi CCTV'); ?>"></div>
              </div>
              <hr>
              <h6>Item Penawaran</h6>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Deskripsi</th>
                      <th>Harga</th>
                      <th>Jumlah</th>
                      <th>Satuan</th>
                      <th class="text-end">Total</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="proposal-items-barang"></tbody>
                  <tbody>
                    <tr>
                      <td colspan="6" class="text-center table-secondary fw-bold">Jasa</td>
                    </tr>
                  </tbody>
                  <tbody id="proposal-items-jasa"></tbody>
                </table>
              </div>
              <button type="button" class="btn btn-sm btn-outline-primary me-2" id="add-barang-btn"><i class="bi bi-plus-lg"></i> Tambah Barang</button>
              <button type="button" class="btn btn-sm btn-outline-success" id="add-jasa-btn"><i class="bi bi-plus-lg"></i> Tambah Jasa</button>
              <div class="row justify-content-end mt-3">
                <div class="col-md-6">
                  <table class="table table-sm table-borderless">
                    <tbody>
                      <tr>
                        <td>Subtotal:</td>
                        <td class="text-end"><strong id="subtotal">Rp 0</strong></td>
                      </tr>
                      <tr>
                        <td>PPN (11%):</td>
                        <td class="text-end"><strong id="ppn">Rp 0</strong></td>
                      </tr>
                      <tr>
                        <td>PPH 22 (1.5%):</td>
                        <td class="text-end"><strong id="pph">Rp 0</strong></td>
                      </tr>
                      <tr>
                        <td colspan="2">
                          <hr class="my-1">
                        </td>
                      </tr>
                      <tr>
                        <td class="fs-5"><strong>Grand Total:</strong></td>
                        <td class="text-end fs-5"><strong id="grandtotal">Rp 0</strong></td>
                      </tr>
                    </tbody>
                  </table>
                  <input type="hidden" name="proposal[subtotal]" id="input-subtotal"><input type="hidden" name="proposal[grand_total]" id="input-grandtotal">
                </div>
              </div>
              <hr>
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-info me-2"><i class="bi bi-save me-1"></i> Simpan Proposal</button>
                <button type="submit" name="action" value="save_and_print" class="btn btn-secondary"><i class="bi bi-printer-fill me-1"></i> Simpan & Cetak</button>
              </div>
            </div>
          </div>
        </form>
      <?php endif; ?>
    </div>

    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-header"><i class="bi bi-cart-plus-fill me-2"></i>Produk & Nilai</div>
        <div class="card-body">
          <div class="form-floating mb-3"><input type="text" class="form-control bg-light" form="deal-form" name="value" id="deal-value" value="<?= $data['value']; ?>" readonly><label for="deal-value">Nilai Total Kesepakatan (Rp)</label></div>
          <hr>
          <div class="mb-2"><label class="form-label">Tambah Produk</label><select id="product-category" class="form-select mb-2">
              <option value="">Pilih Kategori...</option><?php foreach ($data['categories'] as $category): ?><option value="<?= $category->category_id; ?>"><?= htmlspecialchars($category->category_name); ?></option><?php endforeach; ?>
            </select>
            <div class="input-group"><select id="product-list" class="form-select" disabled>
                <option value="">Pilih Produk...</option>
              </select><input type="number" id="product-quantity" class="form-control" value="1" min="1" style="max-width: 70px;"><button type="button" id="add-product-btn" class="btn btn-success"><i class="bi bi-plus-lg"></i></button></div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><i class="bi bi-list-ul me-2"></i>Keranjang Produk</div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <tbody id="cart-items"><?php if (empty($data['deal_products'])): ?><tr id="empty-cart-row">
                  <td colspan="4" class="text-center text-muted py-4">Keranjang masih kosong</td>
                </tr><?php else: ?><?php foreach ($data['deal_products'] as $product): ?><tr data-product-id="<?= $product->product_id; ?>">
                  <td>
                    <p class="mb-0 fw-bold"><?= htmlspecialchars($product->name); ?></p><small class="text-muted">Rp <?= number_format($product->price_per_unit, 0, ',', '.'); ?></small><input type="hidden" form="deal-form" name="products[<?= $product->product_id; ?>][id]" value="<?= $product->product_id; ?>"><input type="hidden" form="deal-form" name="products[<?= $product->product_id; ?>][price]" value="<?= $product->price_per_unit; ?>">
                  </td>
                  <td style="width: 25%;"><input type="number" class="form-control form-control-sm quantity-input" form="deal-form" name="products[<?= $product->product_id; ?>][quantity]" value="<?= $product->quantity; ?>" min="1"></td>
                  <td class="align-middle subtotal">Rp <?= number_format($product->price_per_unit * $product->quantity, 0, ',', '.'); ?></td>
                  <td class="align-middle text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 remove-item-btn"><i class="bi bi-x-lg"></i></button></td>
                </tr><?php endforeach; ?><?php endif; ?></tbody>
          </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center"><strong class="fs-5">Total:</strong><strong class="fs-5" id="grand-total">Rp 0</strong></div>
      </div>

      <div class="d-flex justify-content-end mt-3">
        <a href="<?= BASE_URL; ?>/deals/detail/<?= $data['id']; ?>" class="btn btn-secondary me-2">Batal</a>
        <button type="submit" form="deal-form" class="btn btn-primary"><i class="bi bi-save me-2"></i>Update Kesepakatan</button>
      </div>
    </div>
  </div>

</div>

<script>
  // JavaScript utuh dari sebelumnya, tidak perlu diubah
  document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('proposal-section')) {
      const itemBarangContainer = document.getElementById('proposal-items-barang');
      const itemJasaContainer = document.getElementById('proposal-items-jasa');
      let itemIndex = 0;

      function formatToNumber(str) {
        return parseFloat(String(str).replace(/[^0-9]/g, '')) || 0;
      }

      function formatToRupiah(number) {
        return `Rp ${new Intl.NumberFormat('id-ID').format(Math.round(number))}`;
      }

      function formatToInput(number) {
        return new Intl.NumberFormat('id-ID').format(number);
      }

      function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.proposal-item-row').forEach(row => {
          const price = formatToNumber(row.querySelector('.item-price').value);
          const qty = parseInt(row.querySelector('.item-qty').value) || 0;
          const total = price * qty;
          row.querySelector('.item-total').textContent = formatToRupiah(total);
          subtotal += total;
        });
        const ppn = subtotal * 0.11;
        const pph = subtotal * 0.015;
        const grandtotal = subtotal + ppn + pph;
        document.getElementById('subtotal').textContent = formatToRupiah(subtotal);
        document.getElementById('ppn').textContent = formatToRupiah(ppn);
        document.getElementById('pph').textContent = `Rp ${pph.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        document.getElementById('grandtotal').textContent = `Rp ${grandtotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        document.getElementById('input-subtotal').value = subtotal;
        document.getElementById('input-grandtotal').value = grandtotal;
      }

      function createItemRow(type, item = {}) {
        const container = type === 'barang' ? itemBarangContainer : itemJasaContainer;
        const newRow = document.createElement('tr');
        newRow.classList.add('proposal-item-row');
        const priceValue = item.price ? formatToInput(parseFloat(item.price)) : '';
        newRow.innerHTML = `<td><input type="text" class="form-control form-control-sm" name="proposal[items][${itemIndex}][description]" value="${item.description || ''}" required></td><td><input type="text" class="form-control form-control-sm item-price" name="proposal[items][${itemIndex}][price]" value="${priceValue}" required></td><td><input type="number" class="form-control form-control-sm item-qty" name="proposal[items][${itemIndex}][quantity]" value="${item.quantity || '1'}" min="1" required></td><td><input type="text" class="form-control form-control-sm" name="proposal[items][${itemIndex}][unit]" value="${item.unit || 'Unit'}" required></td><td class="text-end align-middle item-total">Rp 0</td><td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-item-btn"><i class="bi bi-trash"></i></button></td><input type="hidden" name="proposal[items][${itemIndex}][type]" value="${type}">`;
        container.appendChild(newRow);
        itemIndex++;
      }
      document.getElementById('add-barang-btn').addEventListener('click', () => createItemRow('barang'));
      document.getElementById('add-jasa-btn').addEventListener('click', () => createItemRow('jasa'));
      const proposalSection = document.querySelector('#proposal-section');
      proposalSection.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
          e.target.closest('tr').remove();
          calculateTotals();
        }
      });
      proposalSection.addEventListener('input', function(e) {
        if (e.target.classList.contains('item-price')) {
          const value = formatToNumber(e.target.value);
          e.target.value = formatToInput(value);
        }
        if (e.target.classList.contains('item-price') || e.target.classList.contains('item-qty')) {
          calculateTotals();
        }
      });
      const existingItems = <?= json_encode($data['proposal']->items ?? []) ?>;
      if (existingItems.length > 0) {
        existingItems.forEach(item => createItemRow(item.item_type, item));
      }
      calculateTotals();
    }
    const categorySelect = document.getElementById('product-category');
    const productSelect = document.getElementById('product-list');
    const addProductBtn = document.getElementById('add-product-btn');
    const cartItems = document.getElementById('cart-items');
    const grandTotalEl = document.getElementById('grand-total');
    const dealValueInput = document.getElementById('deal-value');
    let productsData = [];
    categorySelect.addEventListener('change', function() {
      const categoryId = this.value;
      productSelect.innerHTML = '<option value="">Memuat...</option>';
      productSelect.disabled = true;
      if (!categoryId) {
        productSelect.innerHTML = '<option value="">Pilih Produk...</option>';
        return;
      }
      fetch(`<?= BASE_URL ?>/api/getProductsByCategory/${categoryId}`).then(response => response.json()).then(data => {
        productsData = data;
        productSelect.disabled = false;
        let options = '<option value="">Pilih Produk...</option>';
        data.forEach(product => {
          options += `<option value="${product.product_id}">${product.name}</option>`;
        });
        productSelect.innerHTML = options;
      });
    });
    addProductBtn.addEventListener('click', function() {
      const productId = productSelect.value;
      const quantity = parseInt(document.getElementById('product-quantity').value);
      if (!productId || !quantity || quantity < 1) return;
      const product = productsData.find(p => p.product_id == productId);
      if (!product || cartItems.querySelector(`tr[data-product-id="${product.product_id}"]`)) return;
      const emptyRow = document.getElementById('empty-cart-row');
      if (emptyRow) emptyRow.remove();
      const subtotal = product.price * quantity;
      const newRowHTML = `<tr data-product-id="${product.product_id}"><td><p class="mb-0 fw-bold">${product.name}</p><small class="text-muted">Rp ${new Intl.NumberFormat('id-ID').format(product.price)}</small><input type="hidden" form="deal-form" name="products[${product.product_id}][id]" value="${product.product_id}"><input type="hidden" form="deal-form" name="products[${product.product_id}][price]" value="${product.price}"></td><td style="width: 25%;"><input type="number" class="form-control form-control-sm quantity-input" form="deal-form" name="products[${product.product_id}][quantity]" value="${quantity}" min="1"></td><td class="align-middle subtotal">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</td><td class="align-middle text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 remove-item-btn"><i class="bi bi-x-lg"></i></button></td></tr>`;
      cartItems.insertAdjacentHTML('beforeend', newRowHTML);
      updateGrandTotal();
    });
    cartItems.addEventListener('click', function(e) {
      if (e.target.closest('.remove-item-btn')) {
        e.target.closest('tr').remove();
        updateGrandTotal();
        if (cartItems.children.length === 0) {
          cartItems.innerHTML = '<tr id="empty-cart-row"><td colspan="4" class="text-center text-muted py-4">Keranjang masih kosong</td></tr>';
        }
      }
    });
    cartItems.addEventListener('input', function(e) {
      if (e.target.classList.contains('quantity-input')) {
        const row = e.target.closest('tr');
        const priceInput = row.querySelector('input[name*="[price]"]');
        if (priceInput) {
          const price = parseFloat(priceInput.value);
          const quantity = parseInt(e.target.value) || 0;
          const subtotal = price * quantity;
          row.querySelector('.subtotal').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}`;
          updateGrandTotal();
        }
      }
    });

    function updateGrandTotal() {
      let total = 0;
      cartItems.querySelectorAll('tr[data-product-id]').forEach(row => {
        const priceInput = row.querySelector('input[name*="[price]"]');
        const quantityInput = row.querySelector('.quantity-input');
        if (priceInput && quantityInput) {
          total += parseFloat(priceInput.value) * (parseInt(quantityInput.value) || 0);
        }
      });
      grandTotalEl.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
      dealValueInput.value = total;
    }
    updateGrandTotal();
  });
</script>