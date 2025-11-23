<?php 
include 'koneksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ambil parameter GET
$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'Bus';
$sort  = isset($_GET['sort']) ? $_GET['sort'] : '';

// Query tiket sesuai jenis
$query = "SELECT * FROM tb_tiket WHERE jenis='$jenis'";

// Sorting
if ($sort == 'Januari' || $sort == 'Februari' || $sort == 'Maret') {
    $query .= " ORDER BY tanggal ASC";
} elseif ($sort == 'harga_tertinggi') {
    $query .= " ORDER BY harga DESC";
} elseif ($sort == 'harga_terendah') {
    $query .= " ORDER BY harga ASC";
}

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tiket</title>
    <link rel="stylesheet" href="component/navbar.css">
    <link rel="stylesheet" href="style/tiket.css">
</head>
<body>

<?php include 'login.php'; ?>

<div class="container">
    <img class="banner-tiket" src="asset/1621823158319-c40708cee7aef086cbd23b5a6e68da3c.webp" alt="">

    <div class="filter-container">
        <div class="left-filter">
            <span class="filter-title">Pilih Jenis Tiket:</span>
            <div class="filter-item-tiket">
                <a href="?jenis=Bus">
                    <button class="filter-tiket-btn <?= $jenis=='Bus' ? 'active-filter' : '' ?>">Bus</button>
                </a>
            </div>
            <div class="filter-item-tiket">
                <a href="?jenis=Pesawat">
                    <button class="filter-tiket-btn <?= $jenis=='Pesawat' ? 'active-filter' : '' ?>">Pesawat</button>
                </a>
            </div>
            <div class="filter-item-tiket">
                <a href="?jenis=Kereta">
                    <button class="filter-tiket-btn <?= $jenis=='Kereta' ? 'active-filter' : '' ?>">Kereta</button>
                </a>
            </div>
        </div>

        <div class="right-filter">
            <div class="drop-down-filter">
                <div class="drop-down-btn" id="sortBtn">
                    URUTKAN: 
                    <?php
                        if ($sort == 'harga_tertinggi') echo 'Harga Tertinggi';
                        elseif ($sort == 'harga_terendah') echo 'Harga Terendah';
                        else echo '✓';
                    ?>
                </div>
                <div class="filter-content" id="sortMenu">
                    <a href="?jenis=<?= $jenis ?>&sort=harga_tertinggi">Harga Tertinggi</a>
                    <a href="?jenis=<?= $jenis ?>&sort=harga_terendah">Harga Terendah</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-list">
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="ticket-card">
                <div class="card-left">
                    <div class="pesawat">
                        <p><?= htmlspecialchars($row['nama_maskapai']); ?></p>
                    </div>
                    <h3><?= htmlspecialchars($row['nama_rute']); ?></h3>
                    <p class="kelas"><?= htmlspecialchars($row['kelas']); ?></p>
                </div>

                <div class="card-middle">
                    <div>
                        <h4><?= htmlspecialchars($row['berangkat_jam']); ?></h4>
                        <p><?= htmlspecialchars($row['dari']); ?></p>
                    </div>

                    <div class="duration">
                        <?= htmlspecialchars($row['durasi']); ?><br>Langsung
                    </div>

                    <div>
                        <h4><?= htmlspecialchars($row['tiba_jam']); ?></h4>
                        <p><?= htmlspecialchars($row['ke']); ?></p>
                    </div>
                </div>

                <div class="card-right">
                    <div class="price">
                        Rp <?= number_format($row['harga'], 0, ',', '.'); ?> <span>/org</span>
                    </div>

                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <!-- BELUM LOGIN: TAMPILKAN ALERT SAAT KLIK -->
                        <button type="button" class="btn-pilih"
                                onclick="alert('Silakan login terlebih dahulu untuk memesan tiket.');">
                            Pilih
                        </button>
                    <?php else: ?>
                        <!-- SUDAH LOGIN: BOLEH LANJUT KE BOOKING -->
                        <a href="booking.php?tiket_id=<?= $row['id']; ?>" class="btn-pilih">
                            Pilih
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <footer>
        <?php include 'component/footer.php'; ?>
    </footer>
</div>

<script>
// Dropdown
const sortBtn = document.getElementById("sortBtn");
const sortMenu = document.getElementById("sortMenu");

sortBtn.onclick = function () {
    sortMenu.classList.toggle("show-dropdown");
};

document.addEventListener("click", function(e){
    if (!e.target.closest(".drop-down-filter")) {
        sortMenu.classList.remove("show-dropdown");
    }
});
</script>

</body>
</html>
