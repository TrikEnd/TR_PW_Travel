<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="component/navbar.css">
    <link rel="stylesheet" href="style/tiket.css">
</head>
<body>
    <?php include 'component/navbar.php'; ?>
    
    <div class="container">

    <img class="banner-tiket" src="asset/1621823158319-c40708cee7aef086cbd23b5a6e68da3c.webp" alt="">


<!-- filter tiket -->

<div class="filter-container">

    <div class="left-filter">
        <span class="filter-title">Pilih Jenis Tiket:</span>

        <div class="filter-item-tiket">
            <button class="filter-tiket-btn active-filter">Bus</button>
        </div>

        <div class="filter-item-tiket">
            <button class="filter-tiket-btn">Pesawat</button>    
        </div>

        <div class="filter-item-tiket">
            <button class="filter-tiket-btn">Kapal</button>
        </div>
</div>


    <div class="right-filter">
    <div class="drop-down-filter">
        <div class="drop-down-btn" id="sortBtn">URUTKAN ✓</div>
        <div class="filter-content" id="sortMenu">
            <a href="">Januari</a>
            <a href="">Februari</a>
            <a href="">Maret</a>
        </div>
    </div>
</div>


</div>

<!-- list tiket -->

<div class="card-list">

    <div class="ticket-card">

        <div class="card-left">
            <div class="pesawat">
                <img src="asset/air-asia.png" alt="">
                <p>AirAsia</p>
            </div>
            <h3>Parahyangan (131)</h3>
            <p class="kelas">Ekonomi (CA)</p>

            <div class="sub-info">
                <a href="#">Detail Perjalanan</a>
                <a href="#">Info</a>
            </div>
        </div>

        <div class="card-middle">
            <div>
                <h4>05:00</h4>
                <p>BD</p>
            </div>

            <div class="duration">3j 0m<br>Langsung</div>

            <div>
                <h4>08:00</h4>
                <p>GMR</p>
            </div>
        </div>

        <div class="card-right">
            <div class="price">Rp 100.000 <span>/orng</span></div>
            <button>Pilih</button>
        </div>

    </div>

</div>


<div class="card-list">

    <div class="ticket-card">

        <div class="card-left">
            <h3>Parahyangan (131)</h3>
            <p class="kelas">Ekonomi (CA)</p>

            <div class="sub-info">
                <a href="#">Detail Perjalanan</a>
                <a href="#">Info</a>
            </div>
        </div>

        <div class="card-middle">
            <div>
                <h4>05:00</h4>
                <p>BD</p>
            </div>

            <div class="duration">3j 0m<br>Langsung</div>

            <div>
                <h4>08:00</h4>
                <p>GMR</p>
            </div>
        </div>

        <div class="card-right">
            <div class="price">Rp 100.000 <span>/pax</span></div>
            <button>Pilih</button>
        </div>

    </div>

</div>

</div>

</body>
</html>

</body>
</html>

<script>
document.getElementById("sortBtn").onclick = 
    function () {
    const menu = document.getElementById("sortMenu");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
};

// Tutup menu jika klik di luar
document.addEventListener("click", 
    function(e){
    if (!e.target.closest(".drop-down-filter")) {
        document.getElementById("sortMenu").style.display = "none";
    }
});


// active button filter tiket

const buttons = document.querySelectorAll('.filter-tiket-btn');

buttons.forEach(btn => {
    btn.addEventListener('click', function() {

        // Hapus active dari semua
        buttons.forEach(b => b.classList.remove('active-filter'));

        // Tambahkan active ke yang diklik
        this.classList.add('active-filter');
    });
});

</script>
