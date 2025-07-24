<?php
// app/controllers/Home.php

class Home extends Controller
{
  public function index()
  {
    // Arahkan ke halaman login
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
  }
}
