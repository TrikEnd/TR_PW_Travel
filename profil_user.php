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

<?php include 'koneksi.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Ambil data user dari database
$sql = "SELECT * FROM tb_user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Jika user tidak ditemukan, redirect ke login
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Proses update profile
if (isset($_POST['update_profile'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_telepon = $_POST['no_telepon'];
    
    $sql = "UPDATE tb_user SET username = ?, email = ?, no_telepon = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nama, $email, $no_telepon, $user_id);
    
    if ($stmt->execute()) {
        $message = "Profil berhasil diperbarui!";
        // Refresh data user
        $stmt = $conn->prepare("SELECT * FROM tb_user WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $error = "Gagal memperbarui profil!";
    }
}

// Proses update password
if (isset($_POST['update_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    // Verifikasi password lama
    if (password_verify($password_lama, $user['password'])) {
        if ($password_baru === $konfirmasi_password) {
            $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
            $sql = "UPDATE tb_user SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($stmt->execute()) {
                $message = "Password berhasil diperbarui!";
            } else {
                $error = "Gagal memperbarui password!";
            }
        } else {
            $error = "Konfirmasi password tidak cocok!";
        }
    } else {
        $error = "Password lama salah!";
    }
}

// Ambil riwayat booking
$sql = "SELECT b.*, t.jenis, t.nama_maskapai, t.dari, t.ke, t.tanggal 
        FROM tb_booking b 
        JOIN tb_tiket t ON b.tiket_id = t.id 
        WHERE b.user_id = ? 
        ORDER BY b.tanggal_booking DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>
>>>>>>> 4a5ebb4c6329c07329aa31e8cfb4a5df68ed9f32
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

    <div class="container">
        <div class="card">
            <h1>Profil Saya</h1>
            <p class="subtitle">Kelola informasi profil Anda</p>
            <?php if ($message): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Informasi Profil</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="no_telepon" value="<?php echo htmlspecialchars($user['no_telepon'] ?? ''); ?>" required>
                </div>
                <button type="submit" name="update_profile">Simpan Perubahan</button>
            </form>
        </div>


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
=======
        <div class="card">
            <h2>Riwayat Pemesanan</h2>
            
            <?php if ($bookings && $bookings->num_rows > 0): ?>
                <?php while ($booking = $bookings->fetch_assoc()): 
                    // Tentukan icon berdasarkan jenis
                    $jenis = $booking['jenis'] ?? 'Pesawat';
                    $icon = 'plane.svg';
                    if ($jenis == 'Bus') $icon = 'bus.svg';
                    elseif ($jenis == 'Kereta') $icon = 'train.svg';
                ?>
                <div class="history-item">
                    <h3>
                        <img src="asset/<?php echo $icon; ?>" alt="<?php echo $jenis; ?>-icon">
                        <?php echo htmlspecialchars($booking['nama_maskapai'] ?? 'N/A'); ?>
                    </h3>
                    <p><?php echo htmlspecialchars($booking['dari'] ?? ''); ?> → <?php echo htmlspecialchars($booking['ke'] ?? ''); ?></p>
                    <p><small>Tanggal Keberangkatan: <?php echo isset($booking['tanggal']) ? date('d M Y', strtotime($booking['tanggal'])) : 'N/A'; ?></small></p>
                    <p>
                        <strong>Rp <?php echo number_format($booking['total_harga'] ?? 0, 0, ',', '.'); ?></strong> | 
                        Dibooking: <?php echo isset($booking['tanggal_booking']) ? date('d M Y', strtotime($booking['tanggal_booking'])) : 'N/A'; ?> | 
                        Kode: <?php echo htmlspecialchars($booking['kode_booking'] ?? 'N/A'); ?>
                    </p>
                    <span class="status"><?php echo htmlspecialchars($booking['status_pembayaran'] ?? 'Belum Bayar'); ?></span>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 20px;">Belum ada riwayat pemesanan</p>
            <?php endif; ?>
>>>>>>> 4a5ebb4c6329c07329aa31e8cfb4a5df68ed9f32
        </div>
    </div>

</div>

</body>
</html>