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
        <span class="filter-title">Filter:</span>

        <div class="filter-item">
            <button class="filter-btn">Waktu ▼</button>
            <div class="dropdown">
                <p>Pagi (04.00 - 11.00)</p>
                <p>Siang (11.00 - 15.00)</p>
                <p>Sore (15.00 - 18.00)</p>
                <p>Malam (18.00 - 24.00)</p>
            </div>
        </div>

        <div class="filter-item">
            <button class="filter-btn">Kelas ▼</button>
            <div class="dropdown">
                <p>Ekonomi</p>
                <p>Bisnis</p>
                <p>Eksekutif</p>
            </div>
        </div>

        <div class="filter-item">
            <button class="filter-btn">Kereta ▼</button>
            <div class="dropdown">
                <p>Argo Parahyangan</p>
                <p>Serayu</p>
                <p>Kutojaya</p>
                <p>Argo Lawu</p>
            </div>
        </div>
    </div>

    <div class="right-filter">
        <button class="sort-btn">URUTKAN ✓</button>
    </div>

</div>

<!-- list tiket -->

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