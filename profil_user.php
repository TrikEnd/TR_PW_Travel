<?php
session_start();
require 'koneksi.php';

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$id = $_SESSION['user_id'];

// ================================
// AMBIL DATA USER
// ================================
$stmt = $conn->prepare("SELECT username, email, no_telepon, created_at FROM tb_user WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// ================================
// UPDATE PROFIL
// ================================
if (isset($_POST['update_profile'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no = $_POST['no_telepon'];

    $stmt = $conn->prepare("UPDATE tb_user SET username=?, email=?, no_telepon=? WHERE id=?");
    $stmt->bind_param("sssi", $nama, $email, $no, $id);
    $stmt->execute();

    // UPDATE SESSION
    $_SESSION['username'] = $nama;

    header("Location: profil_user.php");
    exit;
}
// ================================
// UPDATE PASSWORD
// ================================
if (isset($_POST['update_password'])) {
    $lama = $_POST['password_lama'];
    $baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // Ambil password lama
    $stmt = $conn->prepare("SELECT password FROM tb_user WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $passDb = $stmt->get_result()->fetch_assoc()['password'];

    // Validasi
    if (!password_verify($lama, $passDb)) {
        $error = "Password lama salah";
    } elseif ($baru !== $konfirmasi) {
        $error = "Konfirmasi password tidak sama";
    } else {
        $newPass = password_hash($baru, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE tb_user SET password=? WHERE id=?");
        $stmt->bind_param("si", $newPass, $id);
        $stmt->execute();
        $success = "Password berhasil diperbarui";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User - Travel Ticket</title>

    <!-- Panggil navbar -->
    <link rel="stylesheet" href="component/navbar.css">
    <link rel="stylesheet" href="style/profil_user.css">
</head>
<body>

<?php include 'component/navbar.php'; ?>

<div class="container">

    <!-- PROFIL -->
    <div class="card">
        <h1>Profil Saya</h1>
        <p class="subtitle">Kelola informasi profil Anda</p>
    </div>

    <div class="card">
        <h2>Informasi Profil</h2>

        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?= $user['username'] ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= $user['email'] ?>" required>
            </div>

            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="no_telepon" value="<?= $user['no_telepon'] ?>" required>
            </div>

            <div class="form-group">
                <label>Tanggal Daftar</label>
                <input type="text" value="<?= date('d M Y', strtotime($user['created_at'])) ?>" disabled>
            </div>

            <button type="submit" name="update_profile">Simpan Perubahan</button>
        </form>
    </div>

    <!-- PASSWORD -->
    <div class="card">
        <h2>Ubah Password</h2>

        <?php if (isset($error)): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <p style="color:green;"><?= $success ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Password Lama</label>
                <input type="password" name="password_lama" required>
            </div>

            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="password_baru" required>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="konfirmasi_password" required>
            </div>

            <button type="submit" name="update_password">Update Password</button>
        </form>
    </div>

    <!-- RIWAYAT -->
    <div class="card">
        <h2>Riwayat Pemesanan</h2>

        <div class="history-item">
            <h3><img src="asset/plane.svg"> Garuda Indonesia - Ekonomi</h3>
            <p>Rumah Billie Eilish → Ngawi</p>
            <p><strong>Rp 3.000.000</strong> | 25 Dec 2024 | Kode: PSW001</p>
            <span class="status">Sudah Bayar</span>
        </div>

        <div class="history-item">
            <h3><img src="asset/train.svg"> Argo Parahyangan</h3>
            <p>Rumah Billie Eilish → Ngawi</p>
            <p><strong>Rp 300.000</strong> | 20 Dec 2024 | Kode: KRT001</p>
            <span class="status">Sudah Bayar</span>
        </div>

        <div class="history-item">
            <h3><img src="asset/bus.svg"> Bus Executive</h3>
            <p>Rumah Billie Eilish → Ngawi</p>
            <p><strong>Rp 200.000</strong> | 15 Dec 2024 | Kode: BUS001</p>
            <span class="status">Sudah Bayar</span>
        </div>
    </div>

</div>

</body>
</html>