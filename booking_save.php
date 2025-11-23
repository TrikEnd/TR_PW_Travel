<?php
session_start();
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: flight_list.php');
    exit;
}

// Data dari form booking.php
$tiket_id         = (int) ($_POST['tiket_id'] ?? 0);
$harga_per_tiket  = (int) ($_POST['harga_per_tiket'] ?? 0);
$total_harga      = (int) ($_POST['total_harga'] ?? 0);
$jumlah_penumpang = (int) ($_POST['jumlah_penumpang'] ?? 1);

$nama_kontak   = mysqli_real_escape_string($conn, $_POST['nama_kontak'] ?? '');
$email_kontak  = mysqli_real_escape_string($conn, $_POST['email_kontak'] ?? '');
$no_telepon    = mysqli_real_escape_string($conn, $_POST['no_telepon'] ?? '');

$meal_type = mysqli_real_escape_string($conn, $_POST['meal_type'] ?? '');
$meal_note = mysqli_real_escape_string($conn, $_POST['meal_note'] ?? '');

$titel_arr        = $_POST['titel'] ?? [];
$nama_arr         = $_POST['nama_penumpang'] ?? [];
$tanggal_arr      = $_POST['tanggal_lahir'] ?? [];
$kursi_arr        = $_POST['no_kursi'] ?? [];
$nationality_arr  = $_POST['nationality'] ?? [];

// Cek tiket ada atau tidak
$qTiket = mysqli_query($conn, "SELECT * FROM tb_tiket WHERE id = $tiket_id");
$tiket  = mysqli_fetch_assoc($qTiket);
if (!$tiket) die("Tiket tidak ditemukan.");

// User ID (kalau pakai login, ganti)
$user_id = $_SESSION['user_id'] ?? 1;

// Nama penumpang utama = penumpang pertama
$nama_penumpang_utama = $nama_arr[0] ?? $nama_kontak;

// Generate kode booking
$kode_booking = 'BK' . strtoupper(substr(md5(uniqid()), 0, 6));

// Insert ke tb_booking
$sqlBooking = "
    INSERT INTO tb_booking 
        (user_id, tiket_id, kode_booking, nama_penumpang, 
         nama_kontak, email_kontak, no_telepon, jumlah_penumpang,
         total_harga, meal_type, meal_note, status_pembayaran)
    VALUES 
        ($user_id, $tiket_id, '$kode_booking', '$nama_penumpang_utama',
         '$nama_kontak', '$email_kontak', '$no_telepon', $jumlah_penumpang,
         $total_harga, '$meal_type', '$meal_note', 'Belum Bayar')
";

if (!mysqli_query($conn, $sqlBooking)) {
    die("Gagal menyimpan booking: " . mysqli_error($conn));
}

$booking_id = mysqli_insert_id($conn);

// Insert penumpang
for ($i = 0; $i < $jumlah_penumpang; $i++) {

    $titel = mysqli_real_escape_string($conn, $titel_arr[$i] ?? 'Tn');
    $nama  = mysqli_real_escape_string($conn, $nama_arr[$i] ?? '');
    $tgl   = mysqli_real_escape_string($conn, $tanggal_arr[$i] ?? null);
    $kursi = mysqli_real_escape_string($conn, $kursi_arr[$i] ?? '');
    $nat   = mysqli_real_escape_string($conn, $nationality_arr[$i] ?? '');

    if ($nama === '') continue;

    $sqlPen = "
        INSERT INTO tb_penumpang
            (booking_id, titel, nama_lengkap, tanggal_lahir, nationality, no_kursi)
        VALUES
            ($booking_id, '$titel', '$nama', '$tgl', '$nat', '$kursi')
    ";
    mysqli_query($conn, $sqlPen);
}

// Redirect ke payment
header("Location: payment.php?booking_id=" . $booking_id);
exit;
