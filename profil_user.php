<?php
session_start();
include 'koneksi.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Ambil data admin dari database
$sql = "SELECT * FROM tb_user WHERE id = ? AND role = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Jika admin tidak ditemukan, redirect ke login
if (!$admin) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Hitung statistik
// Total Users
$result_users = $conn->query("SELECT COUNT(*) as total FROM tb_user WHERE role = 'user'");
$total_users = $result_users ? $result_users->fetch_assoc()['total'] : 0;

// Total Pemesanan
$result_bookings = $conn->query("SELECT COUNT(*) as total FROM tb_booking");
$total_bookings = $result_bookings ? $result_bookings->fetch_assoc()['total'] : 0;

// Total Pendapatan
$result_pendapatan = $conn->query("SELECT SUM(total_harga) as total FROM tb_booking WHERE status_pembayaran = 'Sudah Bayar'");
$data_pendapatan = $result_pendapatan ? $result_pendapatan->fetch_assoc() : null;
$pendapatan = ($data_pendapatan && $data_pendapatan['total']) ? $data_pendapatan['total'] : 0;

// Menunggu Verifikasi
$result_pending = $conn->query("SELECT COUNT(*) as total FROM tb_booking WHERE status_pembayaran = 'Pending'");
$pending = $result_pending ? $result_pending->fetch_assoc()['total'] : 0;

// Proses update profile
if (isset($_POST['update_profile'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_telepon = $_POST['no_telepon'];
    
    $sql = "UPDATE tb_user SET username = ?, email = ?, no_telepon = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nama, $email, $no_telepon, $admin_id);
    
    if ($stmt->execute()) {
        $message = "Profil berhasil diperbarui!";
        // Refresh data admin
        $stmt = $conn->prepare("SELECT * FROM tb_user WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
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
    if (password_verify($password_lama, $admin['password'])) {
        if ($password_baru === $konfirmasi_password) {
            $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
            $sql = "UPDATE tb_user SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $hashed_password, $admin_id);
            
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

// Ambil aktivitas terakhir
$sql = "SELECT b.*, t.nama_maskapai, t.jenis, u.username 
        FROM tb_booking b 
        JOIN tb_tiket t ON b.tiket_id = t.id 
        JOIN tb_user u ON b.user_id = u.id 
        ORDER BY b.tanggal_booking DESC 
        LIMIT 10";
$aktivitas = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Travel Ticket</title>
    <link rel="stylesheet" href="component/navbar.css">
    <link rel="stylesheet" href="style/profil_admin.css">
</head>
<body>
    <?php include 'component/navbar.php'; ?>

    <div class="container">
        <div class="header-card">
            <h1>Dashboard Admin</h1>
            <p class="subtitle">Kelola sistem dan data travel ticket</p>
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

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon user-icon"></div>
                <div class="stat-info">
                    <h3>Total Users</h3>
                    <p class="stat-number"><?php echo number_format($total_users); ?></p>
                    <span class="stat-change positive">User terdaftar</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon ticket-icon"></div>
                <div class="stat-info">
                    <h3>Total Pemesanan</h3>
                    <p class="stat-number"><?php echo number_format($total_bookings); ?></p>
                    <span class="stat-change positive">Pemesanan</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon money-icon"></div>
                <div class="stat-info">
                    <h3>Pendapatan</h3>
                    <p class="stat-number">Rp <?php echo number_format($pendapatan / 1000000, 1); ?>M</p>
                    <span class="stat-change positive">Total pendapatan</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pending-icon"></div>
                <div class="stat-info">
                    <h3>Menunggu Verifikasi</h3>
                    <p class="stat-number"><?php echo $pending; ?></p>
                    <span class="stat-change neutral">Perlu ditinjau</span>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Informasi Admin</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($admin['username']); ?>" placeholder="Nama" required>
                </div>
                <div class="form-group">
                    <label>Email Admin</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" placeholder="ex@tarvelticket.com" required>
                </div>
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="no_telepon" value="<?php echo htmlspecialchars($admin['no_telepon'] ?? ''); ?>" placeholder="+62" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="<?php echo ucfirst($admin['role']); ?>" disabled>
                </div>
                <button type="submit" name="update_profile">Simpan Perubahan</button>
            </form>
        </div>

        <div class="card">
            <h2>Keamanan Akun</h2>
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

        <div class="card">
            <h2>Aktivitas Terakhir</h2>
            <div class="activity-list">
                <?php if ($aktivitas && $aktivitas->num_rows > 0): ?>
                    <?php while ($activity = $aktivitas->fetch_assoc()): 
                        // Tentukan icon dan class berdasarkan status
                        $icon_class = 'info';
                        $title = 'Pemesanan Baru';
                        $status = $activity['status_pembayaran'] ?? 'Belum Bayar';
                        if ($status == 'Sudah Bayar') {
                            $icon_class = 'success';
                            $title = 'Pembayaran Berhasil';
                        } elseif ($status == 'Pending') {
                            $icon_class = 'warning';
                            $title = 'Pembayaran Pending';
                        }
                        
                        // Hitung waktu yang lalu
                        $tanggal_booking = $activity['tanggal_booking'] ?? date('Y-m-d H:i:s');
                        $time_diff = time() - strtotime($tanggal_booking);
                        if ($time_diff < 3600) {
                            $time_ago = floor($time_diff / 60) . ' menit yang lalu';
                        } elseif ($time_diff < 86400) {
                            $time_ago = floor($time_diff / 3600) . ' jam yang lalu';
                        } else {
                            $time_ago = floor($time_diff / 86400) . ' hari yang lalu';
                        }
                    ?>
                    <div class="activity-item">
                        <div class="activity-icon <?php echo $icon_class; ?>"></div>
                        <div class="activity-details">
                            <h3><?php echo $title; ?></h3>
                            <p>
                                User "<?php echo htmlspecialchars($activity['username'] ?? 'N/A'); ?>" - 
                                Booking <?php echo htmlspecialchars($activity['kode_booking'] ?? 'N/A'); ?> 
                                (<?php echo htmlspecialchars($activity['nama_maskapai'] ?? 'N/A'); ?>)
                            </p>
                            <span class="time"><?php echo $time_ago; ?></span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 20px;">Belum ada aktivitas</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h2>Aksi Cepat</h2>
            <div class="quick-actions">
                <button class="action-btn blue" onclick="alert('Fitur dalam pengembangan')">Kelola User</button>
                <button class="action-btn orange" onclick="alert('Fitur dalam pengembangan')">Kelola Tiket</button>
                <button class="action-btn green" onclick="alert('Fitur dalam pengembangan')">Laporan Keuangan</button>
                <button class="action-btn purple" onclick="alert('Fitur dalam pengembangan')">Pengaturan Sistem</button>
            </div>
        </div>
    </div>
</body>
</html>

