<div class="container-fluid px-4">
  <div data-aos="fade-up">
    <h1 class="mt-4">Tambah Kesepakatan Baru</h1>
    <ol class="breadcrumb mb-4">
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/dashboard">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= BASE_URL; ?>/deals">Kesepakatan</a></li>
      <li class="breadcrumb-item active">Tambah</li>
    </ol>
  </div>

  <form action="<?= BASE_URL; ?>/deals/add" method="POST" id="deal-form">
    <div class="row">
      <div class="col-lg-7">
        <div class="card mb-4" data-aos="fade-up" data-aos-delay="100">
          <div class="card-header"><i class="bi bi-info-circle-fill me-2"></i>Detail Utama</div>
          <div class="card-body">
            <div class="form-floating mb-3">
              <input type="text" class="form-control <?= (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" id="name" name="name" placeholder="Nama Kesepakatan" value="<?= $data['name']; ?>" required>
              <label for="name">Nama Kesepakatan</label>
              <span class="invalid-feedback"><?= $data['name_err']; ?></span>
            </div>
            <div class="form-floating mb-3">
              <select name="contact_id" id="contact_id" class="form-select <?= (!empty($data['contact_id_err'])) ? 'is-invalid' : ''; ?>" required>
                <option value="" disabled selected>Pilih Kontak...</option>
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
                    <option value="Analisis Kebutuhan" selected>Analisis Kebutuhan</option>
                    <option value="Proposal">Proposal</option>
                    <option value="Negosiasi">Negosiasi</option>
                    <option value="Menang">Menang</option>
                    <option value="Kalah">Kalah</option>
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
      </div>

      <div class="col-lg-5">
        <div class="card mb-4" data-aos="fade-up" data-aos-delay="200">
          <div class="card-header"><i class="bi bi-cart-plus-fill me-2"></i>Produk & Nilai</div>
          <div class="card-body">
            <div class="form-floating mb-3">
              <input type="text" class="form-control bg-light" name="value" id="deal-value" value="0" readonly>
              <label for="deal-value">Nilai Total Kesepakatan (Rp)</label>
            </div>
            <hr>
            <div class="mb-2">
              <label class="form-label">Tambah Produk</label>
              <select id="product-category" class="form-select mb-2">
                <option value="">Pilih Kategori...</option>
                <?php foreach ($data['categories'] as $category): ?>
                  <option value="<?= $category->category_id; ?>"><?= htmlspecialchars($category->category_name); ?></option>
                <?php endforeach; ?>
              </select>
              <div class="input-group">
                <select id="product-list" class="form-select" disabled>
                  <option value="">Pilih Produk...</option>
                </select>
                <input type="number" id="product-quantity" class="form-control" value="1" min="1" style="max-width: 70px;">
                <button type="button" id="add-product-btn" class="btn btn-success"><i class="bi bi-plus-lg"></i></button>
              </div>
            </div>
          </div>
        </div>
        <div class="card" data-aos="fade-up" data-aos-delay="300">
          <div class="card-header"><i class="bi bi-list-ul me-2"></i>Keranjang Produk</div>
          <div class="card-body p-0">
            <table class="table mb-0">
              <tbody id="cart-items">
                <tr id="empty-cart-row">
                  <td colspan="4" class="text-center text-muted py-4">Keranjang masih kosong</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer d-flex justify-content-between align-items-center">
            <strong class="fs-5">Total:</strong>
            <strong class="fs-5" id="grand-total">Rp 0</strong>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-end mt-3" data-aos="fade-up" data-aos-delay="400">
      <a href="<?= BASE_URL; ?>/deals" class="btn btn-secondary me-2">Batal</a>
      <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan Kesepakatan</button>
    </div>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('product-category');
    const productSelect = document.getElementById('product-list');
    const addProductBtn = document.getElementById('add-product-btn');
    const cartItems = document.getElementById('cart-items');
    const grandTotalEl = document.getElementById('grand-total');
    const dealValueInput = document.getElementById('deal-value');
    const emptyCartRow = document.getElementById('empty-cart-row');

    let productsData = []; // Untuk menyimpan data produk dari fetch

    // 1. Fetch produk saat kategori dipilih
    categorySelect.addEventListener('change', function() {
      const categoryId = this.value;
      productSelect.innerHTML = '<option value="">Memuat...</option>';
      productSelect.disabled = true;

      if (!categoryId) {
        productSelect.innerHTML = '<option value="">Pilih Produk...</option>';
        return;
      }

      fetch(`<?= BASE_URL ?>/api/getProductsByCategory/${categoryId}`)
        .then(response => response.json())
        .then(data => {
          productsData = data;
          productSelect.disabled = false;
          let options = '<option value="">Pilih Produk...</option>';
          data.forEach(product => {
            options += `<option value="${product.product_id}">${product.name}</option>`;
          });
          productSelect.innerHTML = options;
        })
        .catch(error => {
          console.error('Error fetching products:', error);
          productSelect.innerHTML = '<option value="">Gagal memuat</option>';
        });
    });

    // 2. Tambah produk ke keranjang
    addProductBtn.addEventListener('click', function() {
      const productId = productSelect.value;
      const quantity = parseInt(document.getElementById('product-quantity').value);

      if (!productId || !quantity || quantity < 1) {
        alert('Silakan pilih produk dan masukkan jumlah yang valid.');
        return;
      }

      const product = productsData.find(p => p.product_id == productId);
      if (!product) return;

      // Cek jika produk sudah ada di keranjang
      if (cartItems.querySelector(`tr[data-product-id="${product.product_id}"]`)) {
        // Bisa pakai SweetAlert2 jika sudah di-load
        alert('Produk ini sudah ada di keranjang.');
        return;
      }

      // Hapus pesan 'keranjang kosong'
      if (emptyCartRow) emptyCartRow.remove();

      const subtotal = product.price * quantity;

      const newRow = `
            <tr data-product-id="${product.product_id}">
                <td>
                    <p class="mb-0 fw-bold">${product.name}</p>
                    <small class="text-muted">Rp ${new Intl.NumberFormat('id-ID').format(product.price)}</small>
                    <input type="hidden" name="products[${product.product_id}][id]" value="${product.product_id}">
                    <input type="hidden" name="products[${product.product_id}][price]" value="${product.price}">
                </td>
                <td style="width: 25%;">
                    <input type="number" class="form-control form-control-sm quantity-input" name="products[${product.product_id}][quantity]" value="${quantity}" min="1">
                </td>
                <td class="align-middle subtotal">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
                <td class="align-middle text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-item-btn"><i class="bi bi-x-lg"></i></button>
                </td>
            </tr>
        `;
      cartItems.insertAdjacentHTML('beforeend', newRow);
      updateGrandTotal();
    });

    // 3. Event delegation untuk hapus item atau ubah kuantitas
    cartItems.addEventListener('click', function(e) {
      if (e.target.closest('.remove-item-btn')) {
        e.target.closest('tr').remove();
        updateGrandTotal();

        // Tampilkan kembali pesan jika keranjang jadi kosong
        if (cartItems.children.length === 0) {
          cartItems.innerHTML = '<tr id="empty-cart-row"><td colspan="4" class="text-center text-muted py-4">Keranjang masih kosong</td></tr>';
        }
      }
    });

    cartItems.addEventListener('input', function(e) {
      if (e.target.classList.contains('quantity-input')) {
        const row = e.target.closest('tr');
        const price = parseFloat(row.querySelector('input[name*="[price]"]').value);
        const quantity = parseInt(e.target.value) || 0;
        const subtotal = price * quantity;
        row.querySelector('.subtotal').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}`;
        updateGrandTotal();
      }
    });

    // 4. Fungsi untuk menghitung total keseluruhan
    function updateGrandTotal() {
      let total = 0;
      cartItems.querySelectorAll('tr[data-product-id]').forEach(row => {
        const price = parseFloat(row.querySelector('input[name*="[price]"]').value);
        const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
        total += price * quantity;
      });
      grandTotalEl.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
      dealValueInput.value = total;
    }
  });
</script>