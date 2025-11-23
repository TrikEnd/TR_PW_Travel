<?php if (!isset($_SESSION['user_id'])): ?>

<!-- ================================= -->
<!-- NAVBAR SEBELUM LOGIN              -->
<!-- ================================= -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Fix</title>
    <link rel="stylesheet" href="style/navbar.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">

        <div class="nav-left">
            <a href="home.php">
                <img src="asset/logo.png" class="logo" alt="Logo">
            </a>
        </div>

        <ul class="nav-menu">
            <li><a href="home.php">Beranda</a></li>
            <li><a href="tiket.php">Tiket</a></li>
            <!-- <li><a href="#">Package</a></li>
            <li><a href="#">Blog</a></li> -->
        </ul>

        <div class="nav-right">
            <a href="#" class="btn login" id="openPopup">Log In</a>
            <a href="#" class="btn register" id="openRegis">Daftar</a>
        </div>

    </div>
</nav>

<div class="navbar-space"></div>
</body>
</html>

<?php else: ?>

<!-- ================================= -->
<!-- NAVBAR SESUDAH LOGIN              -->
<!-- ================================= -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Fix</title>
    <link rel="stylesheet" href="style/navbar.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">

        <div class="nav-left">
            <a href="home.php">
                <img src="asset/logo.png" class="logo" alt="Logo">
            </a>
        </div>

        <ul class="nav-menu">
            <li><a href="home.php">Beranda</a></li>
            <li><a href="tiket.php">Tiket</a></li>
            <!-- <li><a href="#">Package</a></li>
            <li><a href="#">Blog</a></li> -->
        </ul>

        <div class="nav-right">
            <?php
            // Cek user admin
            require_once 'koneksi.php';
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT role FROM tb_user WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            $is_admin = ($user_data && $user_data['role'] == 'admin');
            ?>
            
            <?php if ($is_admin): ?>
                <!-- Tombol untuk Admin -->
                <a href="admin_dashboard.php" class="btn admin-dashboard">
                    Dashboard Admin
                </a>
            <?php else: ?>
                <!-- Tombol untuk User Biasa -->
                <a href="profil_user.php" class="btn profile">
                    Halo, <b><?php echo $_SESSION['username']; ?></b>
                </a>
            <?php endif; ?>
            
            <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin logout?');">Keluar</a>
        </div>

    </div>
</nav>

<div class="navbar-space"></div>
</body>
</html>

<?php endif; ?>