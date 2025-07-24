<?php
// public/buat_hash.php

// Ganti 'password123' dengan password apa pun yang Anda inginkan
$passwordToHash = 'password123';

// Hasilkan hash
$hashedPassword = password_hash($passwordToHash, PASSWORD_DEFAULT);

// Tampilkan hash agar bisa di-copy
echo "Password Anda: " . $passwordToHash . "<br>";
echo "Hash yang dihasilkan: <br>";
echo "<strong>" . htmlspecialchars($hashedPassword) . "</strong>";
