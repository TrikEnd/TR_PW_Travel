<?php  
session_start();
require 'koneksi.php';

// CEK booking_id
if (!isset($_GET['booking_id'])) {
    header("Location: tiket.php");
    exit;
}

$booking_id = (int) $_GET['booking_id'];

// DATA BOOKING + TIKET
$sql = "SELECT b.*, t.nama_maskapai, t.nama_rute, t.jenis, t.kelas, 
               t.tanggal, t.berangkat_jam, t.tiba_jam, t.durasi
        FROM tb_booking b
        JOIN tb_tiket t ON b.tiket_id = t.id
        WHERE b.id = $booking_id";
$q = mysqli_query($conn, $sql);
if (!$q) {
    die("Galat mengambil data booking: " . mysqli_error($conn));
}
$booking = mysqli_fetch_assoc($q);
if (!$booking) {
    die("Data booking tidak ditemukan.");
}

//  PENUMPANG
$passengers = [];
$ps = mysqli_query($conn, "SELECT * FROM tb_penumpang WHERE booking_id = $booking_id");
while ($row = mysqli_fetch_assoc($ps)) {
    $passengers[] = $row;
}

//  "BAYAR SEKARANG"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metode = $_POST['metode'] ?? 'VA';
    $metode = mysqli_real_escape_string($conn, $metode);
    $jumlah = (int) $booking['total_harga'];
    $now    = date('Y-m-d H:i:s');

    $bank = '';
    $kode_pembayaran = '';

    if ($metode === 'VA') {
        $bank = mysqli_real_escape_string($conn, $_POST['bank_va'] ?? '');
        $kode_pembayaran = mysqli_real_escape_string($conn, $_POST['va_number'] ?? '');

        if ($kode_pembayaran === '') {
            $kode_pembayaran = 'VA' . $booking_id . substr(time(), -6);
        }
    } 
    elseif ($metode === 'QRIS') {
        $kode_pembayaran = 'QRIS-' . $booking_id . '-' . time();
    } 
    elseif ($metode === 'Debit') {
        $card_num = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
        $last4 = substr($card_num, -4);
        $kode_pembayaran = 'DEBIT-****' . $last4;
    } 
    else {
        $kode_pembayaran = 'PAY-' . $booking_id . '-' . time();
    }

    $bank_sql = $bank !== '' ? "'$bank'" : "NULL";

    $sqlPay = "INSERT INTO tb_pembayaran 
        (booking_id, metode, bank, kode_pembayaran, jumlah, tanggal_bayar, status)
        VALUES
        ($booking_id, '$metode', $bank_sql, '$kode_pembayaran', $jumlah, '$now', 'Berhasil')";

    if (!mysqli_query($conn, $sqlPay)) {
        die("Gagal menyimpan pembayaran: " . mysqli_error($conn));
    }

    // UPDATE STATUS BOOKING
    mysqli_query($conn, 
        "UPDATE tb_booking 
         SET status_pembayaran = 'Sudah Bayar'
         WHERE id = $booking_id"
    );

    // LANGSUNG KE E-TIKET
    header("Location: etiket.php?kode_booking=" . urlencode($booking['kode_booking']));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran - <?php echo htmlspecialchars($booking['kode_booking']); ?></title>
    <link rel="stylesheet" href="style/payment.css">
    <link rel="stylesheet" href="component/navbar.css">
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="header-left">
        <img src="asset/logo.png" class="header-logo" alt="Logo">
    </div>

    <div class="step-info">
        Langkah 2 / 2 · Pembayaran
    </div>
</div>
<div class="container">

    <!-- PANEL KIRI -->
    <div class="main-panel">
        <form method="post" id="form-payment">

            <!-- PILIH METODE -->
            <div class="card">
                <div class="card-title">Pilih Metode Pembayaran</div>
                <div class="card-subtitle">Silakan pilih salah satu metode berikut untuk menyelesaikan pembayaran.</div>

                <div class="payment-method-list">
                    <label class="payment-option">
                        <input type="radio" name="metode" value="VA" checked>
                        <span>Transfer Bank (Virtual Account)</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="metode" value="QRIS">
                        <span>QRIS</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="metode" value="Debit">
                        <span>Kartu Debit</span>
                    </label>
                </div>
            </div>

            <!-- TRANSFER VA -->
            <div class="card payment-section" id="section-va">
                <div class="card-title">Transfer Bank (VA)</div>
                <div class="card-subtitle">Pilih bank untuk membuat nomor virtual account.</div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Pilih Bank</label>
                        <select name="bank_va" id="bank_va">
                            <option value="">-- Pilih Bank --</option>
                            <option value="BCA">BCA Virtual Account</option>
                            <option value="BNI">BNI Virtual Account</option>
                            <option value="BRI">BRI Virtual Account</option>
                            <option value="Mandiri">Mandiri Virtual Account</option>
                        </select>
                    </div>
                </div>

                <div class="va-box" id="va_box" style="display:none;">
                    <div class="va-label">Nomor Virtual Account Kamu</div>
                    <div class="va-number" id="va_number_display">-</div>
                    <input type="hidden" name="va_number" id="va_number_input">
                    <div class="va-note">
                        Silakan transfer jumlah sesuai rincian harga menggunakan nomor VA ini.
                    </div>
                </div>
            </div>

            <!-- QRIS -->
            <div class="card payment-section" id="section-qris" style="display:none;">
                <div class="card-title">Bayar dengan QRIS</div>
                <div class="card-subtitle">Scan kode QR di bawah menggunakan aplikasi e-wallet kamu.</div>

                <div class="qris-box">
                    <img src="asset/qr2.png" alt="QRIS Code" class="qris-image">
                    <div class="qris-note">
                    buka e-wallet yang kamu gunakan.
                    </div>
                </div>
            </div>

            <!-- DEBIT -->
            <div class="card payment-section" id="section-debit" style="display:none;">
                <div class="card-title">Pembayaran Kartu Debit</div>
                <div class="card-subtitle">Masukkan detail kartu debit kamu.</div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nomor Kartu</label>
                        <input type="text" name="card_number" placeholder="XXXX XXXX XXXX XXXX">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Pemilik Kartu</label>
                        <input type="text" name="card_name" placeholder="Sesuai di kartu">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group-small">
                        <label>Masa Berlaku (MM/YY)</label>
                        <input type="text" name="card_expiry" placeholder="MM/YY">
                    </div>
                    <div class="form-group form-group-small">
                        <label>CVV</label>
                        <input type="password" name="card_cvv" placeholder="XXX" maxlength="4">
                    </div>
                </div>

                <div class="payment-warning">
                    Ini hanya simulasi. Jangan masukkan nomor kartu asli.
                </div>
            </div>

            <!-- TOMBOL -->
            <div class="card">
                <div class="action-row">
                    <button type="button" class="btn btn-outline" onclick="history.back()">Kembali</button>
                    <button type="submit" class="btn btn-primary">Bayar Sekarang</button>
                </div>
            </div>

        </form>
    </div>

    <!-- PANEL KANAN -->
    <div class="side-panel">

        <div class="card">
            <div class="card-title">Ringkasan Booking</div>
            <div class="flight-info">
                <div class="flight-airline"><?php echo htmlspecialchars($booking['nama_maskapai']); ?></div>
                <div class="flight-route"><?php echo htmlspecialchars($booking['nama_rute']); ?></div>

                <div class="mt-2">
                    <div>Jenis Kendaraan: <?php echo htmlspecialchars($booking['jenis']); ?></div>
                    <div>Kelas: <?php echo htmlspecialchars($booking['kelas']); ?></div>
                </div>

                <div class="mt-2">
                    <div>Tanggal: <?php echo htmlspecialchars($booking['tanggal']); ?></div>
                    <div>Berangkat: <?php echo htmlspecialchars($booking['berangkat_jam']); ?></div>
                    <div>Tiba: <?php echo htmlspecialchars($booking['tiba_jam']); ?></div>
                    <div>Durasi: <?php echo htmlspecialchars($booking['durasi']); ?></div>
                </div>

                <div class="mt-2">
                    <div>Kode Booking: <strong><?php echo htmlspecialchars($booking['kode_booking']); ?></strong></div>
                    <div>Kontak: <?php echo htmlspecialchars($booking['nama_kontak']); ?> (<?php echo htmlspecialchars($booking['no_telepon']); ?>)</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Penumpang</div>
            <?php foreach ($passengers as $p): ?>
                <div class="passenger-row">
                    <strong><?php echo htmlspecialchars($p['titel'] . ' ' . $p['nama_lengkap']); ?></strong><br>
                    Kursi: <?php echo $p['no_kursi'] ? htmlspecialchars($p['no_kursi']) : '-'; ?><br>
                    Kewarganegaraan: <?php echo htmlspecialchars($p['nationality'] ?? '-'); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card">
            <div class="card-title">Rincian Harga</div>
            <div class="price-row">
                <span>Penumpang</span>
                <span><?php echo (int)$booking['jumlah_penumpang']; ?> orang</span>
            </div>
            <div class="price-row">
                <span>Harga Dasar</span>
                <span>Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></span>
            </div>
            <div class="price-row total">
                <span>Total Dibayar</span>
                <span>Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></span>
            </div>
        </div>
    </div>

</div>

<script src="js/payment.js"></script>
</body>
</html>
