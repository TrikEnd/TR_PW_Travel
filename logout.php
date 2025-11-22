<?php
session_start();

// Hapus semua session
session_unset();

// Hancurkan session
session_destroy();

// Redirect ke halaman home (atau index)
header("Location: home.php");
exit();
