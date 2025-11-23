
  <!doctype html>
  <html lang="id">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="component/navbar.css">
  <title>Travelly — Pesan Tiket & Hotel</title>

  <style>

   .footer {
    background: #006a7aff;
    color: white;
    padding: 40px 0 20px;
    margin-top: 50px;
    font-family: Arial, sans-serif;
    max-width: 1300px;
}

.footer-container {
    width: 80%;
    margin: auto;
    display: flex;
    justify-content: space-evenly;
    flex-wrap: wrap;
}

.footer-section {
    width: 22%;
    min-width: 180px;
}


.footer-section h3,
.footer-section h4{
    margin-bottom: 10px;
    font-weight: bold;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin: 6px 0;
}

.footer-section ul li a {
    color: #ffffffff;
    text-decoration: none;
    transition: 0.3s;
}

.footer-section ul li a:hover {
    color: #51fff0ff;
}

.social a {
    font-size: 20px;
    margin-right: 10px;
    color: white;
    text-decoration: none;
}

.footer-bottom {
    text-align: center;
    margin-top: 30px;
    border-top: 1px solid rgba(255,255,255,0.3);
    padding-top: 10px;
    font-size: 14px;
    color: #d6eeee;
}

.social a img {
    width: 25px;
    height: 25px;
}

.social p img {
    width: 13px;
    height: 13px;
}
  </style>
  </head>

  <body>
    

    <footer class="footer">
    <div class="footer-container">

        <div class="footer-section">
            <h3>TRVLR</h3>
            <p>Your best partner </p>
            <p>for travel and adventure.</p>
        </div>

        <div class="footer-section">
            <h4>Menu</h4>
            <ul>
                <li><a href="../Home.php">Beranda</a></li>
                <li><a href="../tiket.php">Tiket</a></li>
                
            </ul>
        </div>

        <div class="footer-section">
            <h4>Bantuan</h4>
            <ul>
                <li><a href="#">Bantuan</a></li>
                <li><a href="#">Syarat & Ketentuan</a></li>
                <li><a href="#">Privasi</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Ikuti kami</h4>
            <div class="social">
                <a href="#"><img src="asset/logo/instagram.png" alt=""></a>
                <a href="#"><img src="asset/logo/twitter.png" alt=""></a>
                <br>
                <br>
                <br>
                
                <p class="cp"><img src="asset/logo/phone.png" alt=""> +62 821-3838-9934 </p>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© 2025 TRAV. All rights reserved.</p>
    </div>
</footer>


  </div>
  </body>
  </html>
