<?php
// Session sudah di-start di admin_dashboard.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Koneksi sudah di-require di admin_dashboard.php
if (!isset($conn)) {
    require 'koneksi.php';
}

// Redirect jika belum login atau bukan admin
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$admin_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Ambil data admin dari database
$stmt = $conn->prepare("SELECT username, email, no_telepon, password, role FROM tb_user WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Jika bukan admin, redirect
if (!$admin || $admin['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Proses update profile
if (isset($_POST['update_profile'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_telepon = $_POST['no_telepon'];
    
    $stmt = $conn->prepare("UPDATE tb_user SET username = ?, email = ?, no_telepon = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nama, $email, $no_telepon, $admin_id);
    
    if ($stmt->execute()) {
        $message = "Profil berhasil diperbarui!";
        $_SESSION['username'] = $nama;
        // Refresh data admin
        $stmt = $conn->prepare("SELECT username, email, no_telepon, password, role FROM tb_user WHERE id = ?");
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
    
    if (password_verify($password_lama, $admin['password'])) {
        if ($password_baru === $konfirmasi_password) {
            $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE tb_user SET password = ? WHERE id = ?");
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

// Fungsi untuk mendapatkan path foto profil admin
function getFotoProfilPath($user_id) {
    $extensions = ['jpg', 'jpeg', 'png', 'gif'];
    foreach ($extensions as $ext) {
        $filepath = "asset/uploads/profile_" . $user_id . "." . $ext;
        if (file_exists($filepath)) {
            return $filepath;
        }
    }
    return 'asset/defaultphotoprofile.png';
}

// Proses update foto profil
if (isset($_POST['update_foto'])) {
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['foto_profil']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        $filesize = $_FILES['foto_profil']['size'];
        
        if (!in_array(strtolower($filetype), $allowed)) {
            $error = "Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.";
        } elseif ($filesize > 2 * 1024 * 1024) {
            $error = "Ukuran file terlalu besar! Maksimal 2MB.";
        } else {
            if (!file_exists('asset/uploads')) {
                mkdir('asset/uploads', 0777, true);
            }
            
            // Hapus foto lama
            $extensions = ['jpg', 'jpeg', 'png', 'gif'];
            foreach ($extensions as $ext) {
                $old_file = "asset/uploads/profile_" . $admin_id . "." . $ext;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            $newfilename = 'profile_' . $admin_id . '.' . strtolower($filetype);
            $upload_path = 'asset/uploads/' . $newfilename;
            
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_path)) {
                $message = "Foto profil berhasil diperbarui!";
            } else {
                $error = "Gagal mengupload file!";
            }
        }
    } else {
        $error = "Tidak ada file yang dipilih!";
    }
}

// Dapatkan path foto profil
$foto_profil = getFotoProfilPath($admin_id);

// Statistik untuk dashboard
$stmt = $conn->query("SELECT COUNT(*) as total FROM tb_user WHERE role='user'");
$total_users = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM tb_booking");
$total_bookings = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM tb_tiket");
$total_tiket = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT SUM(total_harga) as total FROM tb_booking WHERE status_pembayaran='Sudah Bayar'");
$total_revenue = $stmt->fetch_assoc()['total'] ?? 0;
?>
<!-- Content Only - Digunakan di dalam admin_dashboard.php -->
<div style="padding: 0;">
    
    <!-- Header -->
    <div class="admin-header">
        <h1>👨‍💼 Admin Dashboard</h1>
        <p class="subtitle">Selamat datang, <?php echo htmlspecialchars($admin['username']); ?>!</p>
    </div>

    <!-- Alert Messages -->
    <?php if ($message): ?>
        <div class="alert alert-success">
            ✓ <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            ✗ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Statistik Dashboard -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?php echo $total_users; ?></h3>
                <p>Total Users</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🎫</div>
            <div class="stat-info">
                <h3><?php echo $total_bookings; ?></h3>
                <p>Total Bookings</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">✈️</div>
            <div class="stat-info">
                <h3><?php echo $total_tiket; ?></h3>
                <p>Total Tiket</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <h3>Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
    </div>

    <!-- Foto Profil -->
    <div class="card">
        <h2>Foto Profil Admin</h2>
        
        <div class="profile-photo-container">
            <img src="<?php echo htmlspecialchars($foto_profil); ?>" 
                 alt="Foto Profil Admin" 
                 class="profile-photo">
            <div class="admin-badge">ADMIN</div>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Pilih Foto Baru</label>
                <input type="file" name="foto_profil" accept="image/*" class="file-input">
                <div class="file-hint">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.</div>
            </div>
            <button type="submit" name="update_foto" class="btn btn-primary">Upload Foto</button>
        </form>
    </div>

    <!-- Informasi Profil -->
    <div class="card">
        <h2>Informasi Profil Admin</h2>

        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>

            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="no_telepon" value="<?php echo htmlspecialchars($admin['no_telepon'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?php echo strtoupper($admin['role']); ?>" disabled>
            </div>

            <button type="submit" name="update_profile" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>

    <!-- Ubah Password -->
    <div class="card">
        <h2>Ubah Password</h2>

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

            <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
        </form>
    </div>

</div>
<!-- End of Profil Admin Content -->
