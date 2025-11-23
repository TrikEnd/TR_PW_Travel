<?php 
session_start();
require 'koneksi.php';

/*
  Ambil ID tiket dari URL:
  - ?tiket_id=
  - ?id_tiket=
  - ?id=
*/
if (isset($_GET['tiket_id'])) {
    $tiket_id = (int) $_GET['tiket_id'];
} elseif (isset($_GET['id_tiket'])) {
    $tiket_id = (int) $_GET['id_tiket'];
} elseif (isset($_GET['id'])) {
    $tiket_id = (int) $_GET['id'];
} else {
    // Kalau dibuka tanpa pilih tiket, balikin ke list
    header("Location: flight_list.php"); // GANTI kalau nama list tiketmu beda
    exit;
}

// Ambil data tiket dari tb_tiket
$sql_tiket  = "SELECT * FROM tb_tiket WHERE id = $tiket_id";
$res_tiket  = mysqli_query($conn, $sql_tiket);

if (!$res_tiket) {
    die("Terjadi kesalahan saat mengambil data tiket: " . mysqli_error($conn));
}

$tiket = mysqli_fetch_assoc($res_tiket);
if (!$tiket) {
    die("Tiket tidak ditemukan di database. (ID: $tiket_id)");
}

$harga_per_tiket = (int) $tiket['harga'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Tiket - <?php echo htmlspecialchars($tiket['nama_rute']); ?></title>
    <link rel="stylesheet" href="style/booking.css">
    <link rel="stylesheet" href="component/navbar.css">
</head>
<body>

<div class="header">
    <div class="header-left">
        <img src="asset/logo.png" class="header-logo" alt="Logo">
    </div>

    <div class="step-info">
        Langkah 1 / 2 · Data Penumpang & Kontak
    </div>
</div>


<div class="container">
    <!-- PANEL KIRI -->
    <div class="main-panel">
        <form action="booking_save.php" method="post" id="form-booking">
            <!-- Hidden untuk backend -->
            <input type="hidden" name="tiket_id" value="<?php echo $tiket_id; ?>">
            <input type="hidden" name="harga_per_tiket" id="harga_per_tiket" value="<?php echo $harga_per_tiket; ?>">
            <input type="hidden" name="total_harga" id="total_harga" value="<?php echo $harga_per_tiket; ?>">
            <input type="hidden" name="jumlah_penumpang" value="1">

            <!-- array 1 elemen (penumpang 1) -->
            <input type="hidden" name="nama_penumpang[]" id="nama_penumpang_1">
            <input type="hidden" name="tanggal_lahir[]" id="tanggal_lahir_1">
            <input type="hidden" name="no_kursi[]" id="no_kursi_1">

            <!-- DATA KONTAK -->
            <div class="card">
                <div class="card-title">
                    Data Kontak
                    <span class="badge">Untuk e-tiket & info perjalanan</span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Kontak<span class="required">*</span></label>
                        <input type="text" name="nama_kontak" required placeholder="Sesuai KTP / Paspor">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email<span class="required">*</span></label>
                        <input type="email" name="email_kontak" required placeholder="email@example.com">
                    </div>
                    <div class="form-group">
                        <label>Nomor Telepon<span class="required">*</span></label>
                        <input type="text" name="no_telepon" required placeholder="08xxxxxxxxxx">
                    </div>
                </div>
            </div>

            <!-- DATA PENUMPANG (1 Dewasa) -->
            <div class="card">
                <div class="card-title">Penumpang 1</div>
                <div class="card-subtitle">Penumpang Dewasa (usia 12 tahun ke atas)</div>

                <!-- Title + First/Last name -->
                <div class="form-row">
                    <div class="form-group form-group-small">
                        <label>Title<span class="required">*</span></label>
                        <select name="titel[]" id="titel_1" required>
                            <option value="">Pilih</option>
                            <option value="Tn">Tn (Mr)</option>
                            <option value="Ny">Ny (Mrs)</option>
                            <option value="Nn">Nn (Ms)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Depan &amp; Tengah (jika ada)<span class="required">*</span></label>
                        <input type="text" id="first_name_1" placeholder="tanpa title dan tanda baca" required>
                        <span class="hint-text">(tanpa title dan tanda baca)</span>
                    </div>

                    <div class="form-group">
                        <label>Nama Belakang / Keluarga<span class="required">*</span></label>
                        <input type="text" id="last_name_1" placeholder="tanpa title dan tanda baca" required>
                        <span class="hint-text">(tanpa title dan tanda baca)</span>
                    </div>
                </div>

                <!-- Tanggal lahir + Kebangsaan -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Lahir<span class="required">*</span></label>
                        <div class="dob-row">
                            <input type="text" id="dob_day_1" class="dob-input" placeholder="HH" maxlength="2" required>
                            <input type="text" id="dob_month_1" class="dob-input dob-month" placeholder="BB" maxlength="2" required>
                            <input type="text" id="dob_year_1" class="dob-input dob-year" placeholder="TTTT" maxlength="4" required>
                        </div>
                        <span class="hint-text">Penumpang Dewasa (usia 12 tahun ke atas)</span>
                    </div>

                    <div class="form-group">
                        <label>Kebangsaan<span class="required">*</span></label>
                        <select name="nationality[]" id="nationality_1" required>
                            <option value="">Pilih kebangsaan</option>
                            <option value="ID">Indonesia</option>
                            <option value="MY">Malaysia</option>
                            <option value="SG">Singapura</option>
                            <option value="US">Amerika Serikat</option>
                            <option value="Other">Lainnya</option>
                        </select>
                    </div>
                </div>

                <!-- Pilih Kursi -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Pemilihan Kursi (opsional)</label>
                        <div class="seat-row">
                            <input type="text" id="seat_input_1" placeholder="Belum memilih kursi" readonly>
                            <button type="button" class="btn btn-outline btn-small" id="btn_choose_seat">
                                Pilih Kursi
                            </button>
                        </div>
                        <span class="hint-text">
                            Kursi dekat jendela diprioritaskan untuk penumpang dengan disabilitas.
                        </span>
                    </div>
                </div>
            </div>

            <!-- REQUEST MAKANAN -->
            <div class="card">
                <div class="card-title">Permintaan Makanan</div>
                <div class="card-subtitle">Beritahu kami jenis makanan atau permintaan khusus kamu.</div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jenis Makanan</label>
                        <select name="meal_type" id="meal_type">
                            <option value="">Tidak ada permintaan khusus</option>
                            <option value="Regular">Makanan Biasa</option>
                            <option value="Vegetarian">Makanan Vegetarian</option>
                            <option value="Vegan">Makanan Vegan</option>
                            <option value="Kids">Makanan Anak</option>
                            <option value="Halal">Makanan Halal</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Permintaan Khusus / Catatan</label>
                        <textarea name="meal_note" id="meal_note" rows="3"
                                  placeholder="Contoh: tidak pedas, alergi kacang, dsb."></textarea>
                    </div>
                </div>
            </div>

            <!-- TOMBOL BAWAH -->
            <div class="card">
                <div class="action-row">
                    <button type="button" class="btn btn-outline" onclick="history.back()">Kembali</button>
                    <button type="submit" class="btn btn-primary">Lanjut ke Pembayaran</button>
                </div>
            </div>
        </form>
    </div>

    <!-- PANEL KANAN -->
    <div class="side-panel">
        <!-- Ringkasan Perjalanan -->
        <div class="card">
            <div class="card-title">Ringkasan Perjalanan</div>
            <div class="flight-info">
                <div class="flight-airline"><?php echo htmlspecialchars($tiket['nama_maskapai']); ?></div>
                <div class="flight-route"><?php echo htmlspecialchars($tiket['nama_rute']); ?></div>

                <div class="mt-2">
                    <div>Jenis Kendaraan: <?php echo htmlspecialchars($tiket['jenis']); ?></div>
                    <div>Kelas: <?php echo htmlspecialchars($tiket['kelas']); ?></div>
                </div>

                <div class="mt-2">
                    <div>Tanggal: <?php echo htmlspecialchars($tiket['tanggal']); ?></div>
                    <div>Berangkat: <?php echo htmlspecialchars($tiket['berangkat_jam']); ?></div>
                    <div>Tiba: <?php echo htmlspecialchars($tiket['tiba_jam']); ?></div>
                    <div>Durasi: <?php echo htmlspecialchars($tiket['durasi']); ?></div>
                </div>
            </div>
        </div>

        <!-- Rincian Harga -->
        <div class="card">
            <div class="card-title">Rincian Harga</div>
            <div class="price-row">
                <span>Dewasa x 1</span>
                <span>Rp <?php echo number_format($harga_per_tiket, 0, ',', '.'); ?></span>
            </div>
            <div class="price-row total">
                <span>Total Harga</span>
                <span>Rp <span id="total_harga_text"><?php echo number_format($harga_per_tiket, 0, ',', '.'); ?></span></span>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KURSI -->
<div class="seat-modal-backdrop" id="seatModalBackdrop">
    <div class="seat-modal">
        <div class="seat-modal-header">
            <span>Pilih Kursi</span>
            <button type="button" class="seat-modal-close" id="seatModalClose">&times;</button>
        </div>
        <div class="seat-modal-body">
            <div class="seat-legend">
                <span class="seat-box seat-available"></span> Tersedia
                <span class="seat-box seat-window"></span> Kursi Jendela (Khusus Disabilitas)
                <span class="seat-box seat-selected"></span> Dipilih
            </div>

            <div class="seat-grid" id="seatGrid">
                <!-- kursi digenerate dari JS -->
            </div>
        </div>
        <div class="seat-modal-footer">
            <button type="button" class="btn btn-outline btn-small" id="seatModalCancel">Batal</button>
            <button type="button" class="btn btn-primary btn-small" id="seatModalApply">Simpan Kursi</button>
        </div>
    </div>
</div>

<script src="js/booking.js"></script>
</body>
</html>
