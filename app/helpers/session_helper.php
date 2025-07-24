<?php
// app/helpers/session_helper.php

function flash($name = '', $message = '', $type = 'success')
{
  if (!empty($name)) {
    // Set session
    if (!empty($message) && empty($_SESSION[$name])) {
      $_SESSION[$name] = $message;
      $_SESSION[$name . '_type'] = $type;
    }
    // Tampilkan sebagai SweetAlert
    elseif (empty($message) && !empty($_SESSION[$name])) {
      $type = !empty($_SESSION[$name . '_type']) ? $_SESSION[$name . '_type'] : 'success';

      // Hasilkan skrip JavaScript untuk SweetAlert
      echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            icon: "' . $type . '",
                            title: "' . ucfirst($type) . '!",
                            text: "' . $_SESSION[$name] . '",
                            showConfirmButton: false,
                            timer: 2000
                        });
                    });
                  </script>';

      // Hapus session setelah ditampilkan
      unset($_SESSION[$name]);
      unset($_SESSION[$name . '_type']);
    }
  }
}
