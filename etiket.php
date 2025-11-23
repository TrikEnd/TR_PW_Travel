<?php
session_start();
require 'koneksi.php';

// kalau tidak ada kode_booking di URL, lempar ke halaman tiket
if (!isset($_GET['kode_booking'])) {
    header("Location: tiket.php");
    exit;
}

$kode_booking = mysqli_real_escape_string($conn, $_GET['kode_booking']);

// Ambil data booking + tiket
$sql = "SELECT b.*, 
               t.nama_maskapai, t.nama_rute, t.jenis, t.kelas,
               t.tanggal, t.berangkat_jam, t.tiba_jam, t.durasi,
               t.dari, t.ke
        FROM tb_booking b
        JOIN tb_tiket t ON b.tiket_id = t.id
        WHERE b.kode_booking = '$kode_booking'";
$q = mysqli_query($conn, $sql);

if (!$q) {
    die('Kesalahan pada query booking: ' . mysqli_error($conn));
}

$booking = mysqli_fetch_assoc($q);
if (!$booking) {
    die('E-ticket tidak ditemukan untuk kode booking tersebut.');
}

// Ambil penumpang
$passengers = [];
$ps = mysqli_query($conn, "SELECT * FROM tb_penumpang WHERE booking_id = " . (int)$booking['id']);
while ($row = mysqli_fetch_assoc($ps)) {
    $passengers[] = $row;
}

// Ambil pembayaran terakhir
$pembayaran = null;
$qp = mysqli_query($conn, "SELECT * FROM tb_pembayaran WHERE booking_id = " . (int)$booking['id'] . " ORDER BY tanggal_bayar DESC LIMIT 1");
if ($qp && mysqli_num_rows($qp) > 0) {
    $pembayaran = mysqli_fetch_assoc($qp);
}

$isPaid = ($booking['status_pembayaran'] === 'Sudah Bayar');

// Tentukan ikon kendaraan
$vehicleIcon = '✈';
if (strtolower($booking['jenis']) === 'bus') {
    $vehicleIcon = '🚌';
} elseif (strtolower($booking['jenis']) === 'kereta') {
    $vehicleIcon = '🚆';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Ticket - <?php echo htmlspecialchars($booking['kode_booking']); ?></title>

    <link rel="stylesheet" href="style/booking.css">
    <link rel="stylesheet" href="style/e.ticket.css">
</head>
<body>

<div class="header">
    <div class="header-left">
        <img src="asset/logo.png" class="header-logo" alt="Logo">
    </div>

    <div class="step-info">
        <!-- isi step masing-masing halaman -->
    </div>
</div>

<div class="container">
    <div class="main-panel">
        
        <div class="ticket-card">
            <div class="ticket-body">

                <!-- ===================================== -->
                <!-- BAGIAN KIRI - TIKET UTAMA -->
                <!-- ===================================== -->
                <div class="ticket-main">

                    <div class="ticket-main-header">
                        <div>
                            <div class="ticket-airline">
                                <span><?php echo $vehicleIcon; ?></span> 
                                &nbsp;<?php echo htmlspecialchars($booking['nama_maskapai']); ?>
                            </div>
                            <div class="ticket-route-name">
                                <?php echo htmlspecialchars($booking['nama_rute']); ?>
                            </div>
                        </div>

                        <div class="ticket-code-block">
                            <div class="ticket-code-label">Kode Booking</div>
                            <div class="ticket-code-value">
                                <?php echo htmlspecialchars($booking['kode_booking']); ?>
                            </div>
                            <div class="ticket-status <?php echo $isPaid ? 'paid' : 'unpaid'; ?>">
                                <?php echo $isPaid ? 'Sudah Bayar' : 'Belum Bayar'; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Rute besar -->
                    <div class="ticket-route-big">
                        <div class="airport-code">
                            <?php echo strtoupper(htmlspecialchars($booking['dari'])); ?>
                        </div>
                        <div class="route-arrow"><?php echo $vehicleIcon; ?></div>
                        <div class="airport-code">
                            <?php echo strtoupper(htmlspecialchars($booking['ke'])); ?>
                        </div>
                    </div>

                    <!-- Waktu keberangkatan dan tiba -->
                    <div class="ticket-time-row">
                        <div>
                            <div class="time-label">Berangkat</div>
                            <div class="time-value"><?php echo htmlspecialchars($booking['berangkat_jam']); ?></div>
                            <div class="time-sub"><?php echo htmlspecialchars($booking['tanggal']); ?></div>
                        </div>
                        <div>
                            <div class="time-label">Tiba</div>
                            <div class="time-value"><?php echo htmlspecialchars($booking['tiba_jam']); ?></div>
                            <div class="time-sub">Durasi: <?php echo htmlspecialchars($booking['durasi']); ?></div>
                        </div>
                        <div>
                            <div class="time-label">Kelas</div>
                            <div class="time-value"><?php echo htmlspecialchars($booking['kelas']); ?></div>
                            <div class="time-sub"><?php echo htmlspecialchars($booking['jenis']); ?></div>
                        </div>
                    </div>

                    <div class="ticket-divider"></div>

                    <!-- Penumpang & kontak -->
                    <div class="ticket-two-col">
                        
                        <!-- Data penumpang -->
                        <div>
                            <div class="ticket-section-title">Penumpang</div>

                            <?php if (count($passengers) == 0): ?>
                                <div class="ticket-row">Tidak ada data penumpang.</div>
                            <?php else: ?>
                                <?php foreach ($passengers as $p): ?>
                                    <div class="ticket-row">
                                        <span class="pass-name">
                                            <?php echo htmlspecialchars($p['titel'] . ' ' . $p['nama_lengkap']); ?>
                                        </span>

                                        <?php if ($p['no_kursi']): ?>
                                            <span class="pass-seat"> · Kursi: <?php echo htmlspecialchars($p['no_kursi']); ?></span>
                                        <?php endif; ?>

                                        <?php if ($p['nationality']): ?>
                                            <span class="pass-nat"> · <?php echo htmlspecialchars($p['nationality']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Kontak -->
                        <div>
                            <div class="ticket-section-title">Kontak</div>
                            <div class="ticket-row"><?php echo htmlspecialchars($booking['nama_kontak']); ?></div>
                            <div class="ticket-row">Telepon: <?php echo htmlspecialchars($booking['no_telepon']); ?></div>
                            <div class="ticket-row">Email: <?php echo htmlspecialchars($booking['email_kontak']); ?></div>
                        </div>
                    </div>

                    <!-- Makanan -->
                    <div class="ticket-section-title">Permintaan Makanan</div>
                    <div class="ticket-row">
                        Jenis Makanan: <?php echo $booking['meal_type'] ? htmlspecialchars($booking['meal_type']) : '-'; ?>
                    </div>
                    <div class="ticket-row">
                        Catatan: <?php echo $booking['meal_note'] ? nl2br(htmlspecialchars($booking['meal_note'])) : '-'; ?>
                    </div>

                    <!-- Pembayaran -->
                    <div class="ticket-section-title">Rincian Pembayaran</div>
                    <div class="ticket-row">
                        Total Dibayar: <strong>Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></strong>
                    </div>

                    <?php if ($pembayaran): ?>
                        <div class="ticket-row">
                            Metode: <?php echo htmlspecialchars($pembayaran['metode']); ?>
                            <?php if ($pembayaran['metode'] === 'VA' && $pembayaran['bank']): ?>
                                · Bank: <?php echo htmlspecialchars($pembayaran['bank']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="ticket-row">Kode Pembayaran: <?php echo htmlspecialchars($pembayaran['kode_pembayaran']); ?></div>
                        <div class="ticket-row">Waktu Pembayaran: <?php echo htmlspecialchars($pembayaran['tanggal_bayar']); ?></div>
                    <?php endif; ?>

                </div>

                <!-- ===================================== -->
                <!-- BAGIAN KANAN - STUB KECIL -->
                <!-- ===================================== -->
                <div class="ticket-stub">
                    <div class="stub-airline">
                        <?php echo $vehicleIcon . ' ' . htmlspecialchars($booking['nama_maskapai']); ?>
                    </div>

                    <div class="stub-route">
                        <?php echo htmlspecialchars($booking['dari']); ?>
                        <span class="stub-arrow">→</span>
                        <?php echo htmlspecialchars($booking['ke']); ?>
                    </div>

                    <div class="stub-code">
                        <?php echo htmlspecialchars($booking['kode_booking']); ?>
                    </div>

                    <div class="stub-date">
                        <?php echo htmlspecialchars($booking['tanggal']); ?>
                    </div>

                    <div class="stub-seat">
                        Kursi: 
                        <?php
                        echo isset($passengers[0]['no_kursi']) && $passengers[0]['no_kursi']
                            ? htmlspecialchars($passengers[0]['no_kursi'])
                            : '-';
                        ?>
                    </div>

                    <!-- QR -->
                    <div class="stub-qr">
                        <div class="stub-qr-inner">
                            <img src="asset/qr1.png" class="qris-image-ticket" alt="QR Code">
                        </div>
                        <div class="stub-qr-text">Scan QR ini di gate (contoh)</div>
                    </div>

                    <!-- Barcode -->
                    <div class="stub-barcode">
                        <div class="barcode-line"></div>
                        <div class="barcode-line short"></div>
                        <div class="barcode-line"></div>
                        <div class="barcode-line tiny"></div>
                        <div class="barcode-line"></div>
                    </div>
                </div>

            </div>

            <!-- Tombol bawah -->
            <div class="ticket-actions">
                <a href="tiket.php" class="btn btn-outline" id="btnAnotherTrip">Pesan Tiket Lain</a>
                <button id="btnPrint" class="btn btn-primary" type="button">Print E-Ticket</button>
            </div>

        </div>

    </div>
</div>

<script src="js/tiket.js"></script>
</body>
</html>
