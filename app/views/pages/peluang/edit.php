<div class="container-fluid px-4">
  <h1 class="mt-4">Edit Kesepakatan</h1>
  <ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/deals">Peluang</a></li>
    <li class="breadcrumb-item active">Edit</li>
  </ol>

  <?php flash('deal_message'); ?>

  <div class="row">
    <div class="col-lg-8">
      <form action="<?= BASE_URL; ?>/deals/edit/<?= $data['id']; ?>" method="POST" id="deal-form">
        <div class="card mb-4">
          <div class="card-header"><i class="bi bi-info-circle-fill me-2"></i>Detail Utama</div>
          <div class="card-body">
            <div class="form-floating mb-3"><input type="text" class="form-control" id="name" name="name" placeholder="Nama Kesepakatan" value="<?= htmlspecialchars($data['name']); ?>" required><label for="name">Nama Kesepakatan</label></div>
            <div class="form-floating mb-3"><select name="contact_id" id="contact_id" class="form-select" required>
                <option value="" disabled>Pilih Kontak...</option><?php foreach ($data['contacts'] as $contact): ?><option value="<?= $contact->contact_id; ?>" <?= ($data['contact_id'] == $contact->contact_id) ? 'selected' : ''; ?>><?= htmlspecialchars($contact->name); ?> (<?= htmlspecialchars($contact->company_name); ?>)</option><?php endforeach; ?>
              </select><label for="contact_id">Kontak Terkait</label></div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-floating mb-3"><select name="stage" id="stage" class="form-select">
                    <option value="Analisis Kebutuhan" <?= ($data['stage'] == 'Analisis Kebutuhan') ? 'selected' : ''; ?>>Analisis Kebutuhan</option>
                    <option value="Proposal" <?= ($data['stage'] == 'Proposal') ? 'selected' : ''; ?>>Proposal</option>
                    <option value="Negosiasi" <?= ($data['stage'] == 'Negosiasi') ? 'selected' : ''; ?>>Negosiasi</option>
                    <option value="Menang" <?= ($data['stage'] == 'Menang') ? 'selected' : ''; ?>>Menang</option>
                    <option value="Kalah" <?= ($data['stage'] == 'Kalah') ? 'selected' : ''; ?>>Kalah</option>
                  </select><label for="stage">Tahapan</label></div>
              </div>
              <div class="col-md-6">
                <div class="form-floating mb-3"><input type="date" class="form-control" id="expected_close_date" name="expected_close_date" value="<?= $data['expected_close_date']; ?>"><label for="expected_close_date">Perkiraan Pembayaran</label></div>
              </div>
            </div>
          </div>
        </div>
        <?php if ($data['stage'] == 'Analisis Kebutuhan'): ?>
          <div class="card mb-4">
            <div class="card-header bg-info text-white"><i class="bi bi-list-check me-2"></i>Catatan Kebutuhan Proyek</div>
            <div class="card-body">
              <div class="form-floating"><textarea class="form-control" placeholder="Catat kebutuhan proyek di sini" id="requirements_notes" name="requirements_notes" style="height: 150px"><?= htmlspecialchars($data['requirements_notes'] ?? ''); ?></textarea><label for="requirements_notes">Tuliskan detail kebutuhan, spesifikasi, dll.</label></div>
            </div>
          </div>
        <?php endif; ?>
      </form>

      <?php if ($data['stage'] == 'Proposal' || $data['stage'] == 'Negosiasi'): ?>
        <form action="<?= BASE_URL; ?>/deals/updateProposal/<?= $data['id']; ?>" method="POST">
          <div class="card mb-4" id="proposal-section">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"><span><i class="bi bi-file-earmark-text-fill me-2"></i>Buat Proposal Penawaran</span></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3"><label class="form-label">Nomor Surat</label><input type="text" class="form-control" name="proposal[proposal_number]" value="<?= htmlspecialchars($data['proposal']->proposal_number ?? '001/MKT-SIS/VII/2025'); ?>"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Lampiran</label><input type="text" class="form-control" name="proposal[attachment]" value="<?= htmlspecialchars($data['proposal']->attachment ?? '1 Berkas'); ?>"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Perihal</label><input type="text" class="form-control" name="proposal[subject]" value="<?= htmlspecialchars($data['proposal']->subject ?? 'Penawaran Harga'); ?>"></div>
              </div>
              <hr>
              <h6>Item Penawaran</h6>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Deskripsi/Keterangan</th>
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
                  <tbody>
                    <tr>
                      <td colspan="6" class="text-center table-warning fw-bold">Negosiasi</td>
                    </tr>
                  </tbody>
                  <tbody id="proposal-items-negosiasi"></tbody>
                  <tbody>
                    <tr>
                      <td colspan="6" class="text-center table-danger fw-bold">Diskon</td>
                    </tr>
                  </tbody>
                  <tbody id="proposal-items-diskon"></tbody>
                </table>
              </div>
              <button type="button" class="btn btn-sm btn-outline-primary me-2" id="add-barang-btn"><i class="bi bi-plus-lg"></i> Tambah Barang</button>
              <button type="button" class="btn btn-sm btn-outline-success me-2" id="add-jasa-btn"><i class="bi bi-plus-lg"></i> Tambah Jasa</button>
              <button type="button" class="btn btn-sm btn-outline-warning me-2" id="add-negosiasi-btn"><i class="bi bi-dash-lg"></i> Tambah Negosiasi</button>
              <button type="button" class="btn btn-sm btn-outline-danger" id="add-diskon-btn"><i class="bi bi-dash-lg"></i> Tambah Diskon</button>
              <div class="row justify-content-end mt-3">
                <div class="col-md-6">
                  <table class="table table-sm table-borderless">
                    <tbody>
                      <tr>
                        <td>Subtotal Barang & Jasa:</td>
                        <td class="text-end"><strong id="subtotal-brg-jasa">Rp 0</strong></td>
                      </tr>
                      <tr>
                        <td>Total Diskon & Negosiasi:</td>
                        <td class="text-end"><strong id="total-diskon" style="color:red;">(Rp 0)</strong></td>
                      </tr>
                      <tr>
                        <td class="fw-bold border-top">Subtotal Akhir:</td>
                        <td class="text-end fw-bold border-top"><strong id="subtotal">Rp 0</strong></td>
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
                  </table><input type="hidden" name="proposal[subtotal]" id="input-subtotal"><input type="hidden" name="proposal[grand_total]" id="input-grandtotal">
                </div>
              </div>
              <hr>
              <div class="d-flex justify-content-end"><button type="submit" class="btn btn-info me-2"><i class="bi bi-save me-1"></i> Simpan Proposal</button><button type="submit" name="action" value="save_and_print" class="btn btn-secondary"><i class="bi bi-printer-fill me-1"></i> Simpan & Cetak</button></div>
            </div>
          </div>
        </form>
      <?php endif; ?>
    </div>
    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-header"><i class="bi bi-cart-plus-fill me-2"></i>Produk & Nilai</div>
        <div class="card-body">
          <div class="form-floating mb-3"><input type="text" class="form-control bg-light" form="deal-form" name="value" id="deal-value" value="Rp <?= number_format($data['value'], 0, ',', '.'); ?>" readonly><label for="deal-value">Nilai Total Kesepakatan (Rp)</label></div>
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
                    <p class="mb-0 fw-bold"><?= htmlspecialchars($product->name); ?></p><input type="text" class="form-control form-control-sm mt-1 price-input" value="<?= number_format($product->price_per_unit, 0, ',', '.'); ?>"><input type="hidden" form="deal-form" name="products[<?= $product->product_id; ?>][id]" value="<?= $product->product_id; ?>"><input type="hidden" class="original-price" form="deal-form" name="products[<?= $product->product_id; ?>][price]" value="<?= $product->price_per_unit; ?>">
                  </td>
                  <td style="width: 25%;"><input type="number" class="form-control form-control-sm quantity-input" form="deal-form" name="products[<?= $product->product_id; ?>][quantity]" value="<?= $product->quantity; ?>" min="1"></td>
                  <td class="align-middle subtotal">Rp <?= number_format($product->price_per_unit * $product->quantity, 0, ',', '.'); ?></td>
                  <td class="align-middle text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 remove-item-btn"><i class="bi bi-x-lg"></i></button></td>
                </tr><?php endforeach; ?><?php endif; ?></tbody>
          </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center"><strong class="fs-5">Total:</strong><strong class="fs-5" id="grand-total">Rp 0</strong></div>
      </div>
      <div class="d-flex justify-content-end mt-3"><a href="<?= BASE_URL; ?>/deals/detail/<?= $data['id']; ?>" class="btn btn-secondary me-2">Batal</a><button type="submit" form="deal-form" class="btn btn-primary"><i class="bi bi-save me-2"></i>Update Kesepakatan</button></div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    function formatToNumber(str) {
      return parseFloat(String(str).replace(/\./g, '')) || 0;
    }

    function formatToRupiah(number) {
      return `Rp ${new Intl.NumberFormat('id-ID').format(Math.round(number))}`;
    }

    function formatToRupiahDiskon(number) {
      return `(Rp ${new Intl.NumberFormat('id-ID').format(Math.round(Math.abs(number)))})`;
    }

    function formatToInput(number) {
      return new Intl.NumberFormat('id-ID').format(Math.round(number));
    }

    if (document.getElementById('proposal-section')) {
      const containers = {
        barang: document.getElementById('proposal-items-barang'),
        jasa: document.getElementById('proposal-items-jasa'),
        negosiasi: document.getElementById('proposal-items-negosiasi'),
        diskon: document.getElementById('proposal-items-diskon')
      };
      let itemIndex = 0;

      function calculateProposalTotals() {
        let subtotalBrgJasa = 0;
        let totalDiskonNego = 0;
        document.querySelectorAll('.proposal-item-row').forEach(row => {
          const price = parseFloat(row.querySelector('.item-price-hidden').value) || 0;
          const qty = parseInt(row.querySelector('input[name*="[quantity]"]').value) || 0;
          const total = price * qty;
          row.querySelector('.item-total').textContent = formatToRupiah(total);
          if (price > 0) {
            subtotalBrgJasa += total;
          } else {
            totalDiskonNego += total;
          }
        });
        const subtotalAkhir = subtotalBrgJasa + totalDiskonNego;
        const ppn = subtotalAkhir > 0 ? subtotalAkhir * 0.11 : 0;
        const pph = subtotalAkhir > 0 ? subtotalAkhir * 0.015 : 0;
        const grandtotal = subtotalAkhir + ppn + pph;

        document.getElementById('subtotal-brg-jasa').textContent = formatToRupiah(subtotalBrgJasa);
        document.getElementById('total-diskon').textContent = formatToRupiahDiskon(totalDiskonNego);
        document.getElementById('subtotal').textContent = formatToRupiah(subtotalAkhir);
        document.getElementById('ppn').textContent = formatToRupiah(ppn);
        document.getElementById('pph').textContent = formatToRupiah(pph);
        document.getElementById('grandtotal').textContent = formatToRupiah(grandtotal);

        document.getElementById('input-subtotal').value = subtotalAkhir;
        document.getElementById('input-grandtotal').value = grandtotal;
      }

      function createItemRow(type, item = {}) {
        const container = containers[type];
        if (!container) return;
        const newRow = document.createElement('tr');
        newRow.classList.add('proposal-item-row');
        const priceValue = item.price ? formatToInput(Math.abs(parseFloat(item.price))) : '';
        const isNegative = (type === 'diskon' || type === 'negosiasi') ? '-' : '';
        const descriptionHTML = `<textarea class="form-control form-control-sm" name="proposal[items][${itemIndex}][description]" placeholder="Rincian/keterangan..." required>${item.description || ''}</textarea>`;
        newRow.innerHTML = `
                <td>${descriptionHTML}</td>
                <td><div class="input-group input-group-sm"><span class="input-group-text">${isNegative}Rp</span><input type="text" class="form-control form-control-sm item-price-display" value="${priceValue}" required></div></td>
                <td><input type="number" class="form-control form-control-sm item-qty" name="proposal[items][${itemIndex}][quantity]" value="${item.quantity || '1'}" min="1" required></td>
                <td><input type="text" class="form-control form-control-sm" name="proposal[items][${itemIndex}][unit]" value="${item.unit || 'Unit'}" required></td>
                <td class="text-end align-middle item-total">Rp 0</td>
                <td class="text-center align-middle"><button type="button" class="btn btn-sm btn-danger remove-item-btn"><i class="bi bi-trash"></i></button></td>
                <input type="hidden" class="item-type" value="${type}">
                <input type="hidden" name="proposal[items][${itemIndex}][type]" value="${type}">
                <input type="hidden" class="item-price-hidden" name="proposal[items][${itemIndex}][price]" value="${item.price || ''}">`;
        container.appendChild(newRow);
        itemIndex++;
      }

      document.getElementById('add-barang-btn').addEventListener('click', () => createItemRow('barang'));
      document.getElementById('add-jasa-btn').addEventListener('click', () => createItemRow('jasa'));
      document.getElementById('add-negosiasi-btn').addEventListener('click', () => createItemRow('negosiasi'));
      document.getElementById('add-diskon-btn').addEventListener('click', () => createItemRow('diskon'));

      const proposalSection = document.querySelector('#proposal-section');
      proposalSection.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
          e.target.closest('tr').remove();
          calculateProposalTotals();
        }
      });
      proposalSection.addEventListener('input', function(e) {
        const target = e.target;
        const row = target.closest('tr');
        if (!row) return;

        if (target.classList.contains('item-price-display')) {
          const type = row.querySelector('.item-type').value;
          let value = formatToNumber(target.value);
          target.value = formatToInput(value);
          if (type === 'diskon' || type === 'negosiasi') {
            value = -Math.abs(value);
          }
          row.querySelector('.item-price-hidden').value = value;
        }
        calculateProposalTotals();
      });

      const existingItems = <?= json_encode($data['proposal']->items ?? []) ?>;
      if (existingItems.length > 0) {
        existingItems.forEach(item => createItemRow(item.item_type, item));
      }
      calculateProposalTotals();
    }

    // --- LOGIC FOR PRODUCT CART SECTION ---
    const categorySelect = document.getElementById('product-category');
    const productSelect = document.getElementById('product-list');
    const addProductBtn = document.getElementById('add-product-btn');
    const cartItems = document.getElementById('cart-items');
    const grandTotalEl = document.getElementById('grand-total');
    const dealValueInput = document.getElementById('deal-value');
    let productsData = [];

    function updateGrandTotal() {
      let total = 0;
      cartItems.querySelectorAll('tr[data-product-id]').forEach(row => {
        const price = formatToNumber(row.querySelector('.price-input').value);
        const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
        const subtotal = price * quantity;
        row.querySelector('.subtotal').textContent = formatToRupiah(subtotal);
        total += subtotal;
      });
      grandTotalEl.textContent = formatToRupiah(total);
      dealValueInput.value = total;
    }

    categorySelect.addEventListener('change', function() {
      const categoryId = this.value;
      if (!categoryId) {
        productSelect.innerHTML = '<option value="">Pilih Produk...</option>';
        productSelect.disabled = true;
        return;
      }
      fetch(`<?= BASE_URL ?>/api/getProductsByCategory/${categoryId}`).then(res => res.json()).then(data => {
        productsData = data;
        productSelect.disabled = false;
        productSelect.innerHTML = '<option value="">Pilih Produk...</option>' + data.map(p => `<option value="${p.product_id}">${p.name}</option>`).join('');
      });
    });

    addProductBtn.addEventListener('click', function() {
      const productId = productSelect.value;
      if (!productId) return;
      const product = productsData.find(p => p.product_id == productId);
      if (!product || cartItems.querySelector(`tr[data-product-id="${productId}"]`)) return;

      const emptyRow = document.getElementById('empty-cart-row');
      if (emptyRow) emptyRow.remove();
      const newRow = document.createElement('tr');
      newRow.dataset.productId = productId;
      newRow.innerHTML = `
        <td>
            <p class="mb-0 fw-bold">${product.name}</p>
            <input type="text" class="form-control form-control-sm mt-1 price-input" value="${formatToInput(product.price)}">
            <input type="hidden" form="deal-form" name="products[${productId}][id]" value="${productId}">
            <input type="hidden" class="original-price" form="deal-form" name="products[${productId}][price]" value="${product.price}">
        </td>
        <td style="width: 25%;"><input type="number" class="form-control form-control-sm quantity-input" form="deal-form" name="products[${productId}][quantity]" value="1" min="1"></td>
        <td class="align-middle subtotal"></td>
        <td class="align-middle text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0 remove-item-btn"><i class="bi bi-x-lg"></i></button></td>`;
      cartItems.appendChild(newRow);
      updateGrandTotal();
    });

    cartItems.addEventListener('input', function(e) {
      if (e.target.matches('.price-input, .quantity-input')) {
        const row = e.target.closest('tr');
        if (e.target.matches('.price-input')) {
          const value = formatToNumber(e.target.value);
          e.target.value = formatToInput(value);
          row.querySelector('.original-price').value = value;
        }
        updateGrandTotal();
      }
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

    updateGrandTotal();
  });
</script>