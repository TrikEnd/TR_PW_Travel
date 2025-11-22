<?php
    include 'koneksi.php';
    
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

 // memanggil koneksi database

$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'Bus';
$sort  = isset($_GET['sort']) ? $_GET['sort'] : '';

$query = "SELECT * FROM tb_tiket WHERE jenis='$jenis'";

// Sorting berdasarkan bulan
if ($sort == 'Januari' || $sort == 'Februari' || $sort == 'Maret') {
    $query .= " ORDER BY tanggal ASC";
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
            <div class="filter-item-tiket"><a href="?jenis=Bus"><button class="filter-tiket-btn <?= $jenis=='Bus'?'active-filter':'' ?>">Bus</button></a></div>
            <div class="filter-item-tiket"><a href="?jenis=Pesawat"><button class="filter-tiket-btn <?= $jenis=='Pesawat'?'active-filter':'' ?>">Pesawat</button></a></div>
            <div class="filter-item-tiket"><a href="?jenis=Kapal"><button class="filter-tiket-btn <?= $jenis=='Kapal'?'active-filter':'' ?>">Kapal</button></a></div>
        </div>

        <div class="right-filter">
            <div class="drop-down-filter">
                <div class="drop-down-btn" id="sortBtn">URUTKAN ✓</div>
                <div class="filter-content" id="sortMenu">
                    <a href="?jenis=<?= $jenis ?>&sort=Januari">Januari</a>
                    <a href="?jenis=<?= $jenis ?>&sort=Februari">Februari</a>
                    <a href="?jenis=<?= $jenis ?>&sort=Maret">Maret</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-list">
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="ticket-card">
                <div class="card-left">
                    <div class="pesawat">
                        <img src="<?= $row['logo'] ?>" alt="">
                        <p><?= $row['nama_maskapai'] ?></p>
                    </div>
                    <h3><?= $row['nama_rute'] ?></h3>
                    <p class="kelas"><?= $row['kelas'] ?></p>

                    <div class="sub-info">
                        <a href="#">Detail Perjalanan</a>
                        <a href="#">Info</a>
                    </div>
                </div>

                <div class="card-middle">
                    <div>
                        <h4><?= $row['berangkat_jam'] ?></h4>
                        <p><?= $row['dari'] ?></p>
                    </div>

                    <div class="duration"><?= $row['durasi'] ?><br>Langsung</div>

                    <div>
                        <h4><?= $row['tiba_jam'] ?></h4>
                        <p><?= $row['ke'] ?></p>
                    </div>
                </div>

                <div class="card-right">
                    <div class="price">Rp <?= number_format($row['harga'],0,',','.') ?> <span>/org</span></div>
                    <button>Pilih</button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
// Dropdown
const sortBtn = document.getElementById("sortBtn");
sortBtn.onclick = function () {
    const menu = document.getElementById("sortMenu");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
};

document.addEventListener("click", function(e){
    if (!e.target.closest(".drop-down-filter")) {
        document.getElementById("sortMenu").style.display = "none";
    }
});
</script>

</body>
</html>
