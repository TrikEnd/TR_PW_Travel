<?php
session_start();
require 'koneksi.php';

// Redirect jika belum login atau bukan admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    header('Location: Home.php');
    exit();
}

// Get active page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Handle Approve/Reject Booking
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

// Handle Confirm Seat
if (isset($_POST['confirm_seat'])) {
    $penumpang_id = $_POST['penumpang_id'];
    $stmt = $conn->prepare("UPDATE tb_penumpang SET seat_status = 'Confirmed' WHERE id = ?");
    $stmt->bind_param("i", $penumpang_id);
    if ($stmt->execute()) {
        $message = "Kursi berhasil dikonfirmasi!";
    }
}

// Statistik untuk dashboard
$stmt = $conn->query("SELECT COUNT(*) as total FROM tb_user WHERE role='user'");
$total_users = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM tb_booking");
$total_bookings = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT COUNT(*) as total FROM tb_booking WHERE status_booking='Pending'");
$pending_bookings = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT SUM(total_harga) as total FROM tb_booking WHERE status_pembayaran='Sudah Bayar'");
$total_revenue = $stmt->fetch_assoc()['total'] ?? 0;

// User dengan disabilitas
$stmt = $conn->query("SELECT * FROM tb_user WHERE disabilitas != 'Tidak' AND role='user' ORDER BY id DESC");
$users_disabilitas = $stmt;

// Booking Pending Approval
$stmt = $conn->query("SELECT b.*, u.username, t.nama_maskapai, t.dari, t.ke, t.jenis 
                      FROM tb_booking b 
                      JOIN tb_user u ON b.user_id = u.id 
                      JOIN tb_tiket t ON b.tiket_id = t.id 
                      WHERE b.status_booking = 'Pending'
                      ORDER BY b.tanggal_booking DESC LIMIT 20");
$pending_approval = $stmt;

// Seat Management - Semua penumpang dengan kursi
$stmt = $conn->query("SELECT p.*, b.kode_booking, b.status_booking, u.username, t.nama_maskapai, t.dari, t.ke, t.jenis
                      FROM tb_penumpang p
                      JOIN tb_booking b ON p.booking_id = b.id
                      JOIN tb_user u ON b.user_id = u.id
                      JOIN tb_tiket t ON b.tiket_id = t.id
                      WHERE p.no_kursi != '' AND p.no_kursi IS NOT NULL
                      ORDER BY p.seat_status, p.id DESC");
$seat_data = $stmt;

// Fungsi getFotoProfilPath ada di profil_admin.php
// Untuk sidebar, kita cek foto profil secara manual
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

<!-- Sidebar -->
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

<!-- Main Content -->
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

    <!-- Alert Messages -->
    <?php if ($message): ?>
        <div class="alert alert-success">✓ <?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">✗ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Content Area -->
    <div class="content-area">
        
        <?php if ($page == 'dashboard'): ?>
            <!-- DASHBOARD PAGE -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3><?php echo $total_users; ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <img src="asset/booking.svg" alt="Booking">
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_bookings; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                
                <div class="stat-card alert-pending">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-info">
                        <h3><?php echo $pending_bookings; ?></h3>
                        <p>Pending Approval</p>
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

            <!-- Recent Bookings -->
            <div class="card">
                <h2>📋 Booking Terbaru (Perlu Approval)</h2>
                <div class="table-container">
                    <table class="data-table">
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
                                    <td class="action-buttons">
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" name="approve_booking" class="btn-approve">✓ Approve</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" name="reject_booking" class="btn-reject">✗ Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="7" style="text-align: center; color: #999;">Tidak ada booking pending</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page == 'bookings'): ?>
            <!-- BOOKINGS MANAGEMENT PAGE -->
            <div class="card">
                <h2>🎫 Kelola Semua Booking</h2>
                <div class="table-container">
                    <table class="data-table">
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
                                    <td class="action-buttons">
                                        <?php if ($booking['status_booking'] == 'Pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" name="approve_booking" class="btn-approve-sm">✓</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" name="reject_booking" class="btn-reject-sm">✗</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="10" style="text-align: center; color: #999;">Belum ada booking</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page == 'seats'): ?>
            <!-- SEAT MONITORING PAGE -->
            <div class="card">
                <h2>💺 Monitoring Kursi</h2>
                <p style="text-align: center; color: #666; margin-bottom: 20px;">
                    Monitor dan kelola pemilihan kursi penumpang secara real-time
                </p>
                
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
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kursi</th>
                                <th>Penumpang</th>
                                <th>Booking</th>
                                <th>User</th>
                                <th>Rute</th>
                                <th>Status Seat</th>
                                <th>Status Booking</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $seat_monitoring = $conn->query("SELECT p.*, b.kode_booking, b.status_booking, u.username, t.nama_maskapai, t.dari, t.ke, t.jenis
                                                            FROM tb_penumpang p
                                                            JOIN tb_booking b ON p.booking_id = b.id
                                                            JOIN tb_user u ON b.user_id = u.id
                                                            JOIN tb_tiket t ON b.tiket_id = t.id
                                                            WHERE p.no_kursi != '' AND p.no_kursi IS NOT NULL
                                                            ORDER BY p.seat_status, p.id DESC");
                            if ($seat_monitoring && $seat_monitoring->num_rows > 0): 
                                while ($seat = $seat_monitoring->fetch_assoc()): 
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
                                    <td><?php echo htmlspecialchars($seat['dari'] . ' → ' . $seat['ke']); ?></td>
                                    <td>
                                        <?php 
                                        $seatStatusClass = 'warning';
                                        if ($seat['seat_status'] == 'Confirmed') $seatStatusClass = 'success';
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
                                    <td class="action-buttons">
                                        <?php if ($seat['seat_status'] == 'Pending' && $seat['status_booking'] == 'Approved'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="penumpang_id" value="<?php echo $seat['id']; ?>">
                                            <button type="submit" name="confirm_seat" class="btn-approve">✓ Confirm</button>
                                        </form>
                                        <?php elseif ($seat['seat_status'] == 'Confirmed'): ?>
                                            <span style="color: #28a745;">✓ Confirmed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="8" style="text-align: center; color: #999;">Belum ada kursi yang dipilih</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page == 'disability'): ?>
            <!-- USER DISABILITAS PAGE -->
            <div class="card">
                <h2>♿ User dengan Disabilitas</h2>
                <p style="text-align: center; color: #666; margin-bottom: 20px;">
                    Monitor penumpang dengan kebutuhan khusus dan berikan pelayanan terbaik
                </p>
                
                <div class="table-container">
                    <table class="data-table">
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
                                    <td><?php echo $user['id']; ?></td>
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
                                <tr><td colspan="5" style="text-align: center; color: #999;">Tidak ada user dengan disabilitas terdaftar</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page == 'profile'): ?>
            <!-- ADMIN PROFILE PAGE -->
            <?php include 'profil_admin.php'; // Include profil admin yang sudah ada ?>

        <?php endif; ?>

    </div>
</div>

</body>
</html>

