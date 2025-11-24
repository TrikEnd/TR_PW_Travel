<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];
$message = '';
$error = '';

if (isset($_GET['msg'])) {
    switch($_GET['msg']) {
        case 'created':
            $message = "Tiket berhasil ditambahkan!";
            break;
        case 'updated':
            $message = "Tiket berhasil diperbarui!";
            break;
        case 'deleted':
            $message = "Tiket berhasil dihapus!";
            break;
    }
}

$stmt = $conn->prepare("SELECT username, email, no_telepon, password, role FROM tb_user WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin || $admin['role'] != 'admin') {
    header('Location: Home.php');
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

if (isset($_POST['approve_booking'])) {
    $booking_id = $_POST['booking_id'];
    $stmt = $conn->prepare("UPDATE tb_booking SET status_booking = 'Approved' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    if ($stmt->execute()) {
        $message = "Booking berhasil di-approve!";
    }
}

if (isset($_POST['reject_booking'])) {
    $booking_id = $_POST['booking_id'];
    $stmt = $conn->prepare("UPDATE tb_booking SET status_booking = 'Rejected' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    if ($stmt->execute()) {
        $message = "Booking berhasil di-reject!";
    }
}

if (isset($_POST['confirm_seat'])) {
    $penumpang_id = $_POST['penumpang_id'];
    $stmt = $conn->prepare("UPDATE tb_penumpang SET seat_status = 'Confirmed' WHERE id = ?");
    $stmt->bind_param("i", $penumpang_id);
    if ($stmt->execute()) {
        $message = "Kursi berhasil dikonfirmasi!";
    }
}

if (isset($_POST['create_tiket'])) {
    $jenis = $_POST['jenis'];
    $nama_maskapai = $_POST['nama_maskapai'];
    $nama_rute = $_POST['nama_rute'];
    $kelas = $_POST['kelas'];
    $dari = $_POST['dari'];
    $ke = $_POST['ke'];
    $berangkat_jam = $_POST['berangkat_jam'];
    $tiba_jam = $_POST['tiba_jam'];
    $durasi = $_POST['durasi'];
    $harga = $_POST['harga'];
    $tanggal = $_POST['tanggal'];
    
    $stmt = $conn->prepare("INSERT INTO tb_tiket (jenis, nama_maskapai, nama_rute, kelas, dari, ke, berangkat_jam, tiba_jam, durasi, harga, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssis", $jenis, $nama_maskapai, $nama_rute, $kelas, $dari, $ke, $berangkat_jam, $tiba_jam, $durasi, $harga, $tanggal);
    if ($stmt->execute()) {
        $message = "Tiket berhasil ditambahkan!";
        header("Location: admin_dashboard.php?page=tickets&msg=created");
        exit();
    } else {
        $error = "Gagal menambahkan tiket!";
    }
}

if (isset($_POST['update_tiket'])) {
    $tiket_id = $_POST['tiket_id'];
    $jenis = $_POST['jenis'];
    $nama_maskapai = $_POST['nama_maskapai'];
    $nama_rute = $_POST['nama_rute'];
    $kelas = $_POST['kelas'];
    $dari = $_POST['dari'];
    $ke = $_POST['ke'];
    $berangkat_jam = $_POST['berangkat_jam'];
    $tiba_jam = $_POST['tiba_jam'];
    $durasi = $_POST['durasi'];
    $harga = $_POST['harga'];
    $tanggal = $_POST['tanggal'];
    
    $stmt = $conn->prepare("UPDATE tb_tiket SET jenis=?, nama_maskapai=?, nama_rute=?, kelas=?, dari=?, ke=?, berangkat_jam=?, tiba_jam=?, durasi=?, harga=?, tanggal=? WHERE id=?");
    $stmt->bind_param("sssssssssisi", $jenis, $nama_maskapai, $nama_rute, $kelas, $dari, $ke, $berangkat_jam, $tiba_jam, $durasi, $harga, $tanggal, $tiket_id);
    if ($stmt->execute()) {
        $message = "Tiket berhasil diperbarui!";
        header("Location: admin_dashboard.php?page=tickets&msg=updated");
        exit();
    } else {
        $error = "Gagal memperbarui tiket!";
    }
}

if (isset($_POST['delete_tiket'])) {
    $tiket_id = $_POST['tiket_id'];
    
    $stmt = $conn->prepare("DELETE FROM tb_tiket WHERE id = ?");
    $stmt->bind_param("i", $tiket_id);
    if ($stmt->execute()) {
        $message = "Tiket berhasil dihapus!";
        header("Location: admin_dashboard.php?page=tickets&msg=deleted");
        exit();
    } else {
        $error = "Gagal menghapus tiket! Mungkin tiket ini sudah digunakan dalam booking.";
    }
}

$edit_tiket = null;
if (isset($_GET['edit_tiket'])) {
    $tiket_id = $_GET['edit_tiket'];
    $stmt = $conn->prepare("SELECT * FROM tb_tiket WHERE id = ?");
    $stmt->bind_param("i", $tiket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_tiket = $result->fetch_assoc();
}

$stmt = $conn->query("SELECT COUNT(*) as total FROM tb_user WHERE role='user'");
$total_users = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM tb_booking");
$total_bookings = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM tb_booking WHERE status_booking='Pending'");
$pending_bookings = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT SUM(total_harga) as total FROM tb_booking WHERE status_pembayaran='Sudah Bayar'");
$total_revenue = $stmt->fetch_assoc()['total'] ?? 0;

$stmt = $conn->query("SELECT * FROM tb_user WHERE disabilitas != 'Tidak' AND role='user' ORDER BY id DESC");
$users_disabilitas = $stmt;

$stmt = $conn->query("SELECT b.*, u.username, t.nama_maskapai, t.dari, t.ke, t.jenis 
                      FROM tb_booking b 
                      JOIN tb_user u ON b.user_id = u.id 
                      JOIN tb_tiket t ON b.tiket_id = t.id 
                      WHERE b.status_booking = 'Pending'
                      ORDER BY b.tanggal_booking DESC LIMIT 20");
$pending_approval = $stmt;

$filter_jenis = isset($_GET['filter_jenis']) ? $_GET['filter_jenis'] : '';
$filter_maskapai = isset($_GET['filter_maskapai']) ? $_GET['filter_maskapai'] : '';

$jenis_changed = isset($_GET['filter_jenis']) && isset($_SESSION['last_filter_jenis']) && $_GET['filter_jenis'] != $_SESSION['last_filter_jenis'];
if ($jenis_changed) {
    $filter_maskapai = '';
}
$_SESSION['last_filter_jenis'] = $filter_jenis;

$jenis_list = $conn->query("SELECT DISTINCT jenis FROM tb_tiket ORDER BY jenis");
$jenis_options = [];
while ($row = $jenis_list->fetch_assoc()) {
    $jenis_options[] = $row['jenis'];
}

$maskapai_list = [];
if ($filter_jenis) {
    $stmt = $conn->prepare("SELECT DISTINCT nama_maskapai FROM tb_tiket WHERE jenis = ? ORDER BY nama_maskapai");
    $stmt->bind_param("s", $filter_jenis);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $maskapai_list[] = $row['nama_maskapai'];
    }
} else {
    $stmt = $conn->query("SELECT DISTINCT nama_maskapai FROM tb_tiket ORDER BY nama_maskapai");
    while ($row = $stmt->fetch_assoc()) {
        $maskapai_list[] = $row['nama_maskapai'];
    }
}

$seat_query = "SELECT p.*, b.kode_booking, b.status_booking, u.username, t.nama_maskapai, t.dari, t.ke, t.jenis
               FROM tb_penumpang p
               JOIN tb_booking b ON p.booking_id = b.id
               JOIN tb_user u ON b.user_id = u.id
               JOIN tb_tiket t ON b.tiket_id = t.id
               WHERE p.no_kursi != '' AND p.no_kursi IS NOT NULL";

if ($filter_jenis) {
    $seat_query .= " AND t.jenis = '" . $conn->real_escape_string($filter_jenis) . "'";
}

if ($filter_maskapai) {
    $seat_query .= " AND t.nama_maskapai = '" . $conn->real_escape_string($filter_maskapai) . "'";
}

$seat_query .= " ORDER BY p.seat_status, p.id DESC";
$seat_data = $conn->query($seat_query);

$foto_profil = 'asset/defaultphotoprofile.png';
$extensions = ['jpg', 'jpeg', 'png', 'gif'];
foreach ($extensions as $ext) {
    $filepath = "asset/uploads/profile_" . $admin_id . "." . $ext;
    if (file_exists($filepath)) {
        $foto_profil = $filepath;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Travel Ticket</title>
    <link rel="stylesheet" href="component/navbar.css">
    <link rel="stylesheet" href="style/admin_dashboard.css">
    <link rel="stylesheet" href="style/profil_admin.css">
</head>
<body>

<?php include 'component/navbar.php'; ?>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="asset/logo.png" alt="Logo" class="sidebar-logo">
        <h3>Admin Panel</h3>
    </div>
    
    <div class="admin-profile">
        <img src="<?php echo htmlspecialchars($foto_profil); ?>" alt="Admin" class="admin-avatar">
        <p class="admin-name"><?php echo htmlspecialchars($admin['username']); ?></p>
        <span class="admin-badge">ADMIN</span>
    </div>
    
    <nav class="sidebar-nav">
        <a href="?page=dashboard" class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
            <img src="asset/dashboard.svg" alt="Dashboard" class="nav-icon"> Dashboard
        </a>
        <a href="?page=bookings" class="nav-link <?php echo $page == 'bookings' ? 'active' : ''; ?>">
            <img src="asset/booking.svg" alt="Booking" class="nav-icon"> Kelola Booking
        </a>
        <a href="?page=tickets" class="nav-link <?php echo $page == 'tickets' ? 'active' : ''; ?>">
            <img src="asset/ticket.svg" alt="Tickets" class="nav-icon"> Kelola Tiket
        </a>
        <a href="?page=seats" class="nav-link <?php echo $page == 'seats' ? 'active' : ''; ?>">
            <img src="asset/seat.svg" alt="Seat" class="nav-icon"> Monitoring Kursi
        </a>
        <a href="?page=disability" class="nav-link <?php echo $page == 'disability' ? 'active' : ''; ?>">
            <img src="asset/disabilitas.svg" alt="Disability" class="nav-icon"> User Disabilitas
        </a>
        <a href="?page=profile" class="nav-link <?php echo $page == 'profile' ? 'active' : ''; ?>">
            <img src="asset/profile.svg" alt="Profile" class="nav-icon"> Profil Admin
        </a>
        <a href="logout.php" class="nav-link logout">
            <img src="asset/logout.svg" alt="Logout" class="nav-icon"> Logout
        </a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h1>
            <?php 
            switch($page) {
                case 'dashboard': 
                    echo '<img src="asset/dashboard.svg" alt="Dashboard"> Dashboard'; 
                    break;
                case 'bookings': 
                    echo '<img src="asset/booking.svg" alt="Booking"> Kelola Booking'; 
                    break;
                case 'tickets': 
                    echo '<img src="asset/ticket.svg" alt="Tickets"> Kelola Tiket'; 
                    break;
                case 'seats': 
                    echo '<img src="asset/seat.svg" alt="Seat"> Monitoring Kursi'; 
                    break;
                case 'disability': 
                    echo '<img src="asset/disabilitas.svg" alt="Disability"> User Disabilitas'; 
                    break;
                case 'profile': 
                    echo '<img src="asset/profile.svg" alt="Profile"> Profil Admin'; 
                    break;
                default: 
                    echo '<img src="asset/dashboard.svg" alt="Dashboard"> Dashboard';
            }
            ?>
        </h1>
        <div class="topbar-right">
            <span class="welcome">Selamat datang, <?php echo htmlspecialchars($admin['username']); ?>!</span>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">✓ <?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">✗ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="content-area">
        
        <?php if ($page == 'dashboard'): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-users">
                        <img src="asset/profile.svg" alt="Users">
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_users; ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-icon-bookings">
                        <img src="asset/booking.svg" alt="Booking">
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_bookings; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                
                <div class="stat-card stat-card-pending">
                    <div class="stat-icon stat-icon-pending">
                        <img src="asset/seat.svg" alt="Pending">
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $pending_bookings; ?></h3>
                        <p>Pending Approval</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-icon-revenue">
                        <img src="asset/revenue.svg" alt="Revenue">
                    </div>
                    <div class="stat-info">
                        <h3>Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
            </div>

            <div class="card card-wide">
                <h2><img src="asset/booking.svg" alt="Booking" class="card-title-icon"> Booking Terbaru (Perlu Approval)</h2>
                <div class="table-container">
                    <table class="data-table table-recent-bookings">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>User</th>
                                <th>Rute</th>
                                <th>Jenis</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $temp_pending = $conn->query("SELECT b.*, u.username, t.nama_maskapai, t.dari, t.ke, t.jenis 
                                                          FROM tb_booking b 
                                                          JOIN tb_user u ON b.user_id = u.id 
                                                          JOIN tb_tiket t ON b.tiket_id = t.id 
                                                          WHERE b.status_booking = 'Pending'
                                                          ORDER BY b.tanggal_booking DESC LIMIT 5");
                            if ($temp_pending && $temp_pending->num_rows > 0): 
                                while ($booking = $temp_pending->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($booking['kode_booking']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['dari'] . ' → ' . $booking['ke']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['jenis']); ?></td>
                                    <td>Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></td>
                                    <td><span class="badge badge-warning">Pending</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <form method="POST">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <button type="submit" name="approve_booking" class="btn-approve">
                                                    <img src="asset/approve.svg" alt="Approve" class="btn-icon"> Approve
                                                </button>
                                            </form>
                                            <form method="POST">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <button type="submit" name="reject_booking" class="btn-reject">
                                                    <img src="asset/reject.svg" alt="Reject" class="btn-icon"> Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="7" class="empty-state">Tidak ada booking pending</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page == 'bookings'): ?>
            <div class="card card-wide">
                <h2><img src="asset/booking.svg" alt="Booking" class="card-title-icon"> Kelola Semua Booking</h2>
                <div class="table-container">
                    <table class="data-table table-all-bookings">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>User</th>
                                <th>Rute</th>
                                <th>Jenis</th>
                                <th>Penumpang</th>
                                <th>Harga</th>
                                <th>Pembayaran</th>
                                <th>Status Booking</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $all_bookings = $conn->query("SELECT b.*, u.username, t.nama_maskapai, t.dari, t.ke, t.jenis 
                                                          FROM tb_booking b 
                                                          JOIN tb_user u ON b.user_id = u.id 
                                                          JOIN tb_tiket t ON b.tiket_id = t.id 
                                                          ORDER BY b.tanggal_booking DESC");
                            if ($all_bookings && $all_bookings->num_rows > 0): 
                                while ($booking = $all_bookings->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($booking['kode_booking']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['dari'] . ' → ' . $booking['ke']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['jenis']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['nama_penumpang']); ?></td>
                                    <td>Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $booking['status_pembayaran'] == 'Sudah Bayar' ? 'success' : 'warning'; ?>">
                                            <?php echo $booking['status_pembayaran']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusClass = 'warning';
                                        if ($booking['status_booking'] == 'Approved') $statusClass = 'success';
                                        if ($booking['status_booking'] == 'Rejected') $statusClass = 'danger';
                                        ?>
                                        <span class="badge badge-<?php echo $statusClass; ?>">
                                            <?php echo $booking['status_booking']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($booking['tanggal_booking'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($booking['status_booking'] == 'Pending'): ?>
                                            <form method="POST">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <button type="submit" name="approve_booking" class="btn-approve-sm">
                                                    <img src="asset/approve.svg" alt="Approve" class="btn-icon-sm">
                                                </button>
                                            </form>
                                            <form method="POST">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <button type="submit" name="reject_booking" class="btn-reject-sm">
                                                    <img src="asset/reject.svg" alt="Reject" class="btn-icon-sm">
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="10" class="empty-state">Belum ada booking</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page == 'tickets'): ?>
            <div class="card card-wide">
                <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 20px;">
                    <button onclick="toggleTiketForm()" class="btn-create" id="btnToggleForm">
                        <img src="asset/approve.svg" alt="Add" class="btn-icon" style="width: 14px; height: 14px; filter: brightness(0) invert(1);"> Tambah Tiket Baru
                    </button>
                </div>

                <div class="table-container">
                    <table class="data-table table-tickets">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Jenis</th>
                                <th>Nama Transportasi</th>
                                <th>Rute</th>
                                <th>Kelas</th>
                                <th>Dari → Ke</th>
                                <th>Jam</th>
                                <th>Durasi</th>
                                <th>Harga</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $all_tickets = $conn->query("SELECT * FROM tb_tiket ORDER BY tanggal DESC, id DESC");
                            if ($all_tickets && $all_tickets->num_rows > 0): 
                                while ($tiket = $all_tickets->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td><?php echo $tiket['id']; ?></td>
                                    <td><?php echo htmlspecialchars($tiket['jenis']); ?></td>
                                    <td><?php echo htmlspecialchars($tiket['nama_maskapai']); ?></td>
                                    <td><?php echo htmlspecialchars($tiket['nama_rute']); ?></td>
                                    <td><?php echo htmlspecialchars($tiket['kelas']); ?></td>
                                    <td><?php echo htmlspecialchars($tiket['dari'] . ' → ' . $tiket['ke']); ?></td>
                                    <td><?php echo htmlspecialchars($tiket['berangkat_jam'] . ' - ' . $tiket['tiba_jam']); ?></td>
                                    <td><?php echo htmlspecialchars($tiket['durasi']); ?></td>
                                    <td>Rp <?php echo number_format($tiket['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo date('d M Y', strtotime($tiket['tanggal'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?page=tickets&edit_tiket=<?php echo $tiket['id']; ?>" class="btn-edit">
                                                <img src="asset/approve.svg" alt="Edit" class="btn-icon-sm"> Edit
                                            </a>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus tiket ini?');">
                                                <input type="hidden" name="tiket_id" value="<?php echo $tiket['id']; ?>">
                                                <button type="submit" name="delete_tiket" class="btn-delete">
                                                    <img src="asset/reject.svg" alt="Delete" class="btn-icon-sm"> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="11" class="empty-state">Belum ada tiket</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div id="tiketForm" class="tiket-form-container" style="margin-top: 30px; <?php echo !$edit_tiket ? 'display: none;' : ''; ?>">
                    <h3 style="text-align: center; margin-bottom: 20px; color: #1f2f56;">
                        <?php echo $edit_tiket ? 'Edit Tiket' : 'Tambah Tiket Baru'; ?>
                    </h3>
                    <form method="POST" class="tiket-form">
                        <?php if ($edit_tiket): ?>
                            <input type="hidden" name="tiket_id" value="<?php echo $edit_tiket['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Jenis Transportasi *</label>
                                <select name="jenis" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="Pesawat" <?php echo ($edit_tiket && $edit_tiket['jenis'] == 'Pesawat') ? 'selected' : ''; ?>>Pesawat</option>
                                    <option value="Bus" <?php echo ($edit_tiket && $edit_tiket['jenis'] == 'Bus') ? 'selected' : ''; ?>>Bus</option>
                                    <option value="Kereta" <?php echo ($edit_tiket && $edit_tiket['jenis'] == 'Kereta') ? 'selected' : ''; ?>>Kereta</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Nama Transportasi *</label>
                                <input type="text" name="nama_maskapai" value="<?php echo $edit_tiket ? htmlspecialchars($edit_tiket['nama_maskapai']) : ''; ?>" required placeholder="Contoh: Garuda Indonesia">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nama Rute *</label>
                                <input type="text" name="nama_rute" value="<?php echo $edit_tiket ? htmlspecialchars($edit_tiket['nama_rute']) : ''; ?>" required placeholder="Contoh: Jakarta - Bali">
                            </div>
                            
                            <div class="form-group">
                                <label>Kelas *</label>
                                <input type="text" name="kelas" value="<?php echo $edit_tiket ? htmlspecialchars($edit_tiket['kelas']) : ''; ?>" required placeholder="Contoh: Business, Ekonomi">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Dari *</label>
                                <input type="text" name="dari" value="<?php echo $edit_tiket ? htmlspecialchars($edit_tiket['dari']) : ''; ?>" required placeholder="Contoh: Jakarta">
                            </div>
                            
                            <div class="form-group">
                                <label>Ke *</label>
                                <input type="text" name="ke" value="<?php echo $edit_tiket ? htmlspecialchars($edit_tiket['ke']) : ''; ?>" required placeholder="Contoh: Bali">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Jam Berangkat *</label>
                                <input type="time" name="berangkat_jam" value="<?php echo $edit_tiket ? htmlspecialchars($edit_tiket['berangkat_jam']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Jam Tiba *</label>
                                <input type="time" name="tiba_jam" value="<?php echo $edit_tiket ? htmlspecialchars($edit_tiket['tiba_jam']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Durasi *</label>
                                <input type="text" name="durasi" value="<?php echo $edit_tiket ? htmlspecialchars($edit_tiket['durasi']) : ''; ?>" required placeholder="Contoh: 2h, 3h 30m">
                            </div>
                            
                            <div class="form-group">
                                <label>Harga (Rp) *</label>
                                <input type="number" name="harga" value="<?php echo $edit_tiket ? $edit_tiket['harga'] : ''; ?>" required min="0" placeholder="Contoh: 2500000">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Tanggal Keberangkatan *</label>
                                <input type="date" name="tanggal" value="<?php echo $edit_tiket ? htmlspecialchars($edit_tiket['tanggal']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="<?php echo $edit_tiket ? 'update_tiket' : 'create_tiket'; ?>" class="btn-submit">
                                <img src="asset/approve.svg" alt="Save" class="btn-icon"> <?php echo $edit_tiket ? 'Update Tiket' : 'Tambah Tiket'; ?>
                            </button>
                            <?php if ($edit_tiket): ?>
                                <a href="?page=tickets" class="btn-cancel" onclick="document.getElementById('tiketForm').style.display='none'; return true;">Batal</a>
                            <?php else: ?>
                                <button type="button" onclick="toggleTiketForm()" class="btn-cancel">Batal</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

        <?php elseif ($page == 'seats'): ?>
            <div class="card card-wide">
                <h2><img src="asset/seat.svg" alt="Seat" class="card-title-icon"> Monitoring Kursi</h2>
                <p class="card-description">
                    Monitor dan kelola pemilihan kursi penumpang secara real-time
                </p>
                
                <div class="filter-container">
                    <form method="GET" action="admin_dashboard.php" class="filter-form">
                        <input type="hidden" name="page" value="seats">
                        
                        <div class="filter-group">
                            <label for="filter_jenis">Jenis Transportasi:</label>
                            <select name="filter_jenis" id="filter_jenis" class="filter-select">
                                <option value="">Semua Jenis</option>
                                <?php foreach ($jenis_options as $jenis): ?>
                                    <option value="<?php echo htmlspecialchars($jenis); ?>" <?php echo $filter_jenis == $jenis ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($jenis); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="filter_maskapai">Nama Transportasi:</label>
                            <select name="filter_maskapai" id="filter_maskapai" class="filter-select">
                                <option value="">Semua Transportasi</option>
                                <?php foreach ($maskapai_list as $maskapai): ?>
                                    <option value="<?php echo htmlspecialchars($maskapai); ?>" <?php echo $filter_maskapai == $maskapai ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($maskapai); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="btn-filter">Filter</button>
                            <a href="?page=seats" class="btn-reset">Reset</a>
                        </div>
                    </form>
                </div>
                
                <div class="legend">
                    <div class="legend-item">
                        <div class="seat available"></div>
                        <span>Available</span>
                    </div>
                    <div class="legend-item">
                        <div class="seat pending"></div>
                        <span>Pending</span>
                    </div>
                    <div class="legend-item">
                        <div class="seat booked"></div>
                        <span>Confirmed</span>
                    </div>
                </div>

                <div class="table-container">
                    <table class="data-table table-seats">
                        <thead>
                            <tr>
                                <th>Kursi</th>
                                <th>Penumpang</th>
                                <th>Booking</th>
                                <th>User</th>
                                <th>Jenis</th>
                                <th>Nama Transportasi</th>
                                <th>Rute</th>
                                <th>Status Seat</th>
                                <th>Status Booking</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($seat_data && $seat_data->num_rows > 0): 
                                while ($seat = $seat_data->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td>
                                        <div class="seat-number <?php echo strtolower($seat['seat_status']); ?>">
                                            <?php echo htmlspecialchars($seat['no_kursi']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($seat['nama_lengkap']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($seat['kode_booking']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($seat['username']); ?></td>
                                    <td><?php echo htmlspecialchars($seat['jenis']); ?></td>
                                    <td><?php echo htmlspecialchars($seat['nama_maskapai']); ?></td>
                                    <td><?php echo htmlspecialchars($seat['dari'] . ' → ' . $seat['ke']); ?></td>
                                    <td>
                                        <?php 
                                        $seatStatusClass = 'warning';
                                        if ($seat['seat_status'] == 'Confirmed') $seatStatusClass = 'primary';
                                        if ($seat['seat_status'] == 'Available') $seatStatusClass = 'info';
                                        ?>
                                        <span class="badge badge-<?php echo $seatStatusClass; ?>">
                                            <?php echo $seat['seat_status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $bookingStatusClass = 'warning';
                                        if ($seat['status_booking'] == 'Approved') $bookingStatusClass = 'success';
                                        if ($seat['status_booking'] == 'Rejected') $bookingStatusClass = 'danger';
                                        ?>
                                        <span class="badge badge-<?php echo $bookingStatusClass; ?>">
                                            <?php echo $seat['status_booking']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($seat['seat_status'] == 'Pending' && $seat['status_booking'] == 'Approved'): ?>
                                            <form method="POST">
                                                <input type="hidden" name="penumpang_id" value="<?php echo $seat['id']; ?>">
                                                <button type="submit" name="confirm_seat" class="btn-approve">
                                                    <img src="asset/approve.svg" alt="Confirm" class="btn-icon"> Confirm
                                                </button>
                                            </form>
                                            <?php elseif ($seat['seat_status'] == 'Confirmed'): ?>
                                                <span class="status-confirmed">
                                                    <img src="asset/approve.svg" alt="Confirmed" class="btn-icon-inline"> Confirmed
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="10" class="empty-state">Belum ada kursi yang dipilih<?php echo $filter_jenis || $filter_maskapai ? ' dengan filter yang dipilih' : ''; ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page == 'disability'): ?>
            <div class="card">
                <h2><img src="asset/disabilitas.svg" alt="Disability" class="card-title-icon"> User dengan Disabilitas</h2>
                <p class="card-description">
                    Monitor penumpang dengan kebutuhan khusus dan berikan pelayanan terbaik
                </p>
                
                <div class="table-container">
                    <table class="data-table table-disability">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th>Jenis Disabilitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $users_disability = $conn->query("SELECT * FROM tb_user WHERE disabilitas != 'Tidak' AND role='user' ORDER BY id DESC");
                            if ($users_disability && $users_disability->num_rows > 0): 
                                while ($user = $users_disability->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td><strong><?php echo $user['id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['no_telepon'] ?? '-'); ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars($user['disabilitas']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="5" class="empty-state">Tidak ada user dengan disabilitas terdaftar</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page == 'profile'): ?>
            <?php include 'profil_admin.php'; ?>

        <?php endif; ?>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterJenis = document.getElementById('filter_jenis');
    const filterMaskapai = document.getElementById('filter_maskapai');
    const filterForm = document.querySelector('.filter-form');
    let lastJenisValue = filterJenis ? filterJenis.value : '';
    
    if (filterJenis && filterForm) {
        filterJenis.addEventListener('change', function() {
            if (this.value !== lastJenisValue) {
                filterMaskapai.value = '';
                lastJenisValue = this.value;
            }
        });
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('edit_tiket')) {
        const formElement = document.getElementById('tiketForm');
        if (formElement) {
            formElement.style.display = 'block';
            setTimeout(function() {
                formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        }
    }
});

function toggleTiketForm() {
    const form = document.getElementById('tiketForm');
    const btn = document.getElementById('btnToggleForm');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
        setTimeout(function() {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
        if (btn) {
            btn.innerHTML = '<img src="asset/reject.svg" alt="Cancel" class="btn-icon" style="width: 14px; height: 14px; filter: brightness(0) invert(1);"> Batal';
            btn.style.background = '#6c757d';
        }
    } else {
        form.style.display = 'none';
        if (btn) {
            btn.innerHTML = '<img src="asset/approve.svg" alt="Add" class="btn-icon" style="width: 14px; height: 14px; filter: brightness(0) invert(1);"> Tambah Tiket Baru';
            btn.style.background = '#28a745';
        }
        window.location.href = '?page=tickets';
    }
}
</script>

</body>
</html>

