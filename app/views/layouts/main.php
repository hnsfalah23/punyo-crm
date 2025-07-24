<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Punyo CRM</title>

  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Animate On Scroll (AOS) CSS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- Custom CSS (jika diperlukan nanti) -->
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }
  </style>
</head>

<body>

  <div class="container mt-5">
    <div class="card" data-aos="fade-up">
      <div class="card-body text-center">
        <h1 class="card-title">🚀 Selamat Datang di Punyo CRM!</h1>
        <p class="card-text">Setup proyek berhasil. Kita siap untuk langkah selanjutnya.</p>
        <button id="test-sweetalert" class="btn btn-primary">Tes SweetAlert</button>
      </div>
    </div>
  </div>

  <!-- Bootstrap 5.3 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Animate On Scroll (AOS) JS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <!-- Inisialisasi AOS & Custom JS -->
  <script>
    // Inisialisasi AOS
    AOS.init();

    // Tes SweetAlert
    document.getElementById('test-sweetalert').addEventListener('click', function() {
      Swal.fire({
        title: 'Berhasil!',
        text: 'SweetAlert2 berjalan dengan baik!',
        icon: 'success',
        confirmButtonText: 'Keren'
      });
    });
  </script>
</body>

</html>