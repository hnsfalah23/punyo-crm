</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<script>
  // Skrip Global - Dijalankan di semua halaman
  document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi AOS (Animate on Scroll)
    AOS.init({
      once: true,
      duration: 800
    });

    // Logika untuk lihat/sembunyikan password
    document.querySelectorAll('.toggle-password').forEach(toggle => {
      toggle.addEventListener('click', function() {
        const targetSelector = this.dataset.target;
        const passwordInput = document.querySelector(targetSelector);
        if (passwordInput) {
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          this.querySelector('i').classList.toggle('bi-eye');
          this.querySelector('i').classList.toggle('bi-eye-slash');
        }
      });
    });

    // Logika untuk konfirmasi hapus
    document.querySelectorAll('.form-delete').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        const itemName = this.dataset.itemName || 'item ini';
        Swal.fire({
          title: 'Apakah Anda yakin?',
          text: "Anda akan menghapus '" + itemName + "' secara permanen!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            this.submit();
          }
        });
      });
    });

    // JavaScript untuk halaman Target
    const monthPicker = document.getElementById('month-picker');
    const typePicker = document.getElementById('type-picker');

    function updateTargetUrl() {
      if (!monthPicker || !typePicker) return;
      const selectedMonth = monthPicker.value;
      const selectedType = typePicker.value;
      window.location.href = `<?= BASE_URL ?>/targets?month=${selectedMonth}&type=${selectedType}`;
    }

    if (monthPicker) {
      monthPicker.addEventListener('change', updateTargetUrl);
    }
    if (typePicker) {
      typePicker.addEventListener('change', updateTargetUrl);
    }

    const dropdowns = document.querySelectorAll('.has-dropdown > a');
    dropdowns.forEach(function(dropdown) {
      dropdown.addEventListener('click', function(e) {
        e.preventDefault();
        const parentLi = this.parentElement;
        parentLi.classList.toggle('open');
      });
    });
  });
</script>
</body>