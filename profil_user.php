<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

$stmt = $conn->prepare("SELECT username, email, no_telepon, password, disabilitas FROM tb_user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    session_destroy();
    header('Location: index.php');
    exit();
}

if (isset($_POST['update_profile'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_telepon = $_POST['no_telepon'];
    $disabilitas = $_POST['disabilitas'] ?? 'Tidak';
    
    $stmt = $conn->prepare("UPDATE tb_user SET username = ?, email = ?, no_telepon = ?, disabilitas = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nama, $email, $no_telepon, $disabilitas, $user_id);
    
    if ($stmt->execute()) {
        $message = "Profil berhasil diperbarui!";
        $_SESSION['username'] = $nama;
        $stmt = $conn->prepare("SELECT username, email, no_telepon, password, disabilitas FROM tb_user WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $error = "Gagal memperbarui profil!";
    }
}

if (isset($_POST['update_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    
    if (password_verify($password_lama, $user['password'])) {
        if ($password_baru === $konfirmasi_password) {
            $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE tb_user SET password = ? WHERE id = ?");
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

if (isset($_POST['update_foto'])) {
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['foto_profil']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        $filesize = $_FILES['foto_profil']['size'];
        
        if (!in_array(strtolower($filetype), $allowed)) {
            $error = "Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.";
        }
        elseif ($filesize > 2 * 1024 * 1024) {
            $error = "Ukuran file terlalu besar! Maksimal 2MB.";
        }
        else {
            if (!file_exists('asset/uploads')) {
                mkdir('asset/uploads', 0777, true);
            }
            
            $extensions = ['jpg', 'jpeg', 'png', 'gif'];
            foreach ($extensions as $ext) {
                $old_file = "asset/uploads/profile_" . $user_id . "." . $ext;
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            
            $newfilename = 'profile_' . $user_id . '.' . strtolower($filetype);
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

$foto_profil = getFotoProfilPath($user_id);

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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User - Travel Ticket</title>

    <link rel="stylesheet" href="style/profil_user.css">
</head>
<body>

<div class="container">

    <div style="margin-bottom: 20px;">
        <a href="Home.php" style="display: inline-block; padding: 10px 20px; background: #0046ff; color: white; text-decoration: none; border-radius: 8px; font-size: 14px; transition: 0.3s;">
            ← Kembali ke Beranda
        </a>
    </div>

    <div class="card">
        <h1>Profil Saya</h1>
        <p class="subtitle">Kelola informasi profil Anda</p>
        
        <?php if ($message): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Foto Profil</h2>
        
        <div class="profile-photo-container">
            <img src="<?php echo htmlspecialchars($foto_profil); ?>" 
                 alt="Foto Profil" 
                 class="profile-photo">
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Pilih Foto Baru</label>
                <input type="file" name="foto_profil" accept="image/*" class="file-input">
                <div class="file-hint">
                    Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                </div>
            </div>
            <button type="submit" name="update_foto">Upload Foto</button>
        </form>
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

            <div class="form-group">
                <label>Kebutuhan Khusus / Disabilitas</label>
                <select name="disabilitas">
                    <option value="Tidak" <?php echo ($user['disabilitas'] ?? 'Tidak') == 'Tidak' ? 'selected' : ''; ?>>Tidak Ada</option>
                    <option value="Kursi Roda" <?php echo ($user['disabilitas'] ?? '') == 'Kursi Roda' ? 'selected' : ''; ?>>Kursi Roda</option>
                    <option value="Tuna Netra" <?php echo ($user['disabilitas'] ?? '') == 'Tuna Netra' ? 'selected' : ''; ?>>Tuna Netra</option>
                    <option value="Tuna Rungu" <?php echo ($user['disabilitas'] ?? '') == 'Tuna Rungu' ? 'selected' : ''; ?>>Tuna Rungu</option>
                    <option value="Lainnya" <?php echo ($user['disabilitas'] ?? '') == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                </select>
            </div>

            <button type="submit" name="update_profile">Simpan Perubahan</button>
        </form>
    </div>

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

            <button type="submit" name="update_password">Update Password</button>
        </form>
    </div>

    <div class="card">
        <h2>Riwayat Pemesanan</h2>
        
        <?php if ($bookings && $bookings->num_rows > 0): ?>
            <?php while ($booking = $bookings->fetch_assoc()): 
                $jenis = $booking['jenis'] ?? 'Pesawat';
                $icon = 'plane.svg';
                if ($jenis == 'Bus') $icon = 'bus.svg';
                elseif ($jenis == 'Kereta') $icon = 'train.svg';
            ?>
            <div class="history-item">
                <h3>
                    <img src="asset/<?php echo htmlspecialchars($icon); ?>" alt="<?php echo htmlspecialchars($jenis); ?>-icon">
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
    </div>

</div>

</body>
</html>
