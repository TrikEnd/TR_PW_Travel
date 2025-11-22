
<?php if (!isset($_SESSION['user_id'])): ?>

<!-- ================================= -->
<!-- NAVBAR SAAT BELUM LOGIN           -->
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
            <li><a href="home.php">Home</a></li>
            <li><a href="tiket.php">Tiket</a></li>
            <li><a href="#">Package</a></li>
            <li><a href="#">Blog</a></li>
        </ul>

        <div class="nav-right">
            <a href="#" class="btn login" id="openPopup">Login</a>
            <a href="#" class="btn register" id="openRegis">Register</a>
        </div>

    </div>
</nav>

<div class="navbar-space"></div>
</body>
</html>

<?php else: ?>

<!-- ================================= -->
<!-- NAVBAR SAAT SUDAH LOGIN           -->
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
            <li><a href="home.php">Home</a></li>
            <li><a href="tiket.php">Tiket</a></li>
            <li><a href="#">Package</a></li>
            <li><a href="#">Blog</a></li>
        </ul>

        <div class="nav-right">
            <a href="profil_user.php" class="btn profile">
                Halo, <b><?php echo $_SESSION['username']; ?></b>
            </a>
            <a href="logout.php" class="btn logout">Logout</a>
        </div>

    </div>
</nav>

<div class="navbar-space"></div>
</body>
</html>

<?php endif; ?>
