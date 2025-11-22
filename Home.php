<?php
// Anda bisa menambahkan proses backend di sini jika dibutuhkan
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="component/navbar.css">
<title>Travelly — Pesan Tiket & Hotel</title>

<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  :root {
    --bg:#f6fbff;
    --accent:#1e90ff;
    --accent-2:#00a0ff;
    --muted:#6b7280;
    --card:#fff;
    --radius:12px;
    --shadow:0 6px 18px rgba(16,24,40,.08);
    font-family:Inter,ui-sans-serif,system-ui;
  }

  body {
    background:linear-gradient(180deg,var(--bg),#eef7ff 60%);
    color:#0f172a;
    min-height:100vh;
  }

  .container { max-width:1150px; margin:28px auto; padding:0 20px; }

  header {
    display:flex; justify-content:space-between; align-items:center;
    padding:18px 0;
  }

  .brand { display:flex; align-items:center; gap:14px; }
  .logo {
    width:44px; height:44px; border-radius:10px;
    background:linear-gradient(135deg,var(--accent),var(--accent-2));
    display:flex; justify-content:center; align-items:center;
    font-weight:700; color:#fff; box-shadow:var(--shadow);
  }

  
  .cta {
    padding:10px 14px; border-radius:10px;
    background:transparent; border:1px solid rgba(30,144,255,.12);
    color:var(--accent); font-weight:700;
  }

  .hero {
    margin-top:18px;
    display:grid; gap:28px;
    grid-template-columns:1fr 420px;
  }

  .hero-left {
    padding:28px; border-radius:18px;
    background:rgba(255,255,255,.85);
    box-shadow:var(--shadow);
  }

  h1 { font-size:28px; margin-bottom:8px; }
  .lead { color:var(--muted); margin-bottom:18px; }

  .tabs { display:flex; gap:8px; margin-bottom:18px; }
  .tab {
    padding:10px 12px; border-radius:12px; cursor:pointer;
    background:transparent; color:var(--muted); font-weight:700;
    border:1px solid transparent;
  }
  .tab.active {
    background:linear-gradient(90deg,var(--accent),var(--accent-2));
    color:#fff; box-shadow:0 8px 24px rgba(0,160,255,.12);
  }

  .search-row { display:flex; gap:12px; margin-bottom:12px; }
  .field {
    flex:1; padding:10px 12px;
    background:var(--card); border-radius:12px;
    border:1px solid rgba(15,23,42,.04);
  }
  .field.small { max-width:170px; }

  .search-actions { display:flex; gap:12px; margin-top:8px; }

  .btn-primary {
    padding:12px 16px; border-radius:12px; border:0;
    font-weight:800; cursor:pointer; color:#fff;
    background:linear-gradient(90deg,var(--accent),var(--accent-2));
    box-shadow:0 10px 30px rgba(30,144,255,.12);
  }
  .btn-ghost {
    padding:12px 16px; border-radius:12px; cursor:pointer;
    background:transparent; border:1px solid rgba(15,23,42,.06);
  }

  .promo {
    padding:18px; border-radius:18px; box-shadow:var(--shadow);
    background:linear-gradient(180deg,#fff3,#fff1);
    border:1px solid rgba(15,23,42,.03);
    backdrop-filter:blur(6px);
  }
  .promo h3 { margin-bottom:8px; font-size:16px; }
  .promo p { margin-bottom:12px; color:var(--muted); font-size:14px; }
  .pill {
    display:inline-block; padding:8px 10px;
    border-radius:999px; background:rgba(0,160,255,.08);
    font-weight:700; color:var(--accent-2); font-size:13px;
  }

  .section { margin-top:26px; }
  .cards-grid {
    display:grid; gap:18px; margin-top:14px;
    grid-template-columns:repeat(3,1fr);
  }

  .card {
    background:var(--card); border-radius:14px;
    padding:14px; box-shadow:var(--shadow);
    border:1px solid rgba(15,23,42,.03);
  }
  .card img {
    width:100%; height:130px; object-fit:cover;
    border-radius:10px; margin-bottom:10px;
  }
  .card h4 { font-size:15px; margin-bottom:6px; }
  .muted { color:var(--muted); font-size:13px; }

  .feature-strip {
    margin-top:20px; display:flex; gap:12px;
  }
  .feature {
    flex:1; padding:14px; border-radius:12px; text-align:center;
    background:#fff; box-shadow:var(--shadow);
  }
  .feature strong { display:block; margin-bottom:6px; font-size:18px; }

  footer {
    margin-top:36px; padding:26px 0;
    display:flex; justify-content:space-between; align-items:center;
    color:var(--muted); font-size:13px;
  }
  footer .links { display:flex; gap:14px; }

  @media(max-width:980px){
    .hero{ grid-template-columns:1fr; }
    .cards-grid{ grid-template-columns:repeat(2,1fr); }
  }
  @media(max-width:620px){
    nav{ display:none; }
    .cards-grid{ grid-template-columns:1fr; }
    .feature-strip{ flex-direction:column; }
    .search-row{ flex-direction:column; }
    .field.small{ max-width:100%; }
  }
</style>
</head>

<body>
<div class="container">

<?php include 'component/navbar.php' ?>

  <!-- ==== Hero ==== -->
  <main class="hero">

    <section class="hero-left">
      <h1>Pesan tiket pesawat, hotel, & aktivitas</h1>
      <p class="lead">Cari, bandingkan, lalu pesan — cepat dan aman.</p>

      <div class="tabs">
        <div class="tab active">Penerbangan</div>
        <div class="tab">Hotel</div>
        <div class="tab">Aktivitas</div>
        <div class="tab">Kereta</div>
      </div>

      <form onsubmit="event.preventDefault()">
        <div class="search-row">
          <div class="field">
            <label style="font-size:12px;color:var(--muted);margin-bottom:6px;display:block">
              Keberangkatan
            </label>
            <input type="text" placeholder="Jakarta (CGK)" style="width:100%;border:0;outline:0;font-weight:700">
          </div>

          <div class="field">
            <label style="font-size:12px;color:var(--muted);margin-bottom:6px;display:block">
              Tujuan
            </label>
            <input type="text" placeholder="Bali (DPS)" style="width:100%;border:0;outline:0;font-weight:700">
          </div>

          <div class="field small">
            <label style="font-size:12px;color:var(--muted);margin-bottom:6px;display:block">
              Tanggal
            </label>
            <input type="text" placeholder="22 Nov - 26 Nov" style="width:100%;border:0;outline:0;font-weight:700">
          </div>

          <div class="field small">
            <label style="font-size:12px;color:var(--muted);margin-bottom:6px;display:block">
              Penumpang
            </label>
            <input type="text" placeholder="1 Dewasa, Ekonomi" style="width:100%;border:0;outline:0;font-weight:700">
          </div>
        </div>

        <div class="search-actions">
          <button class="btn-primary">Cari Penerbangan</button>
          <button class="btn-ghost">Pencarian Lanjutan</button>
        </div>
      </form>

      <!-- Penawaran -->
      <div class="section">
        <div style="display:flex;justify-content:space-between;align-items:end">
          <h3 style="font-size:16px">Penawaran Populer</h3>
          <div class="muted">Lihat semua</div>
        </div>

        <div class="cards-grid">
          <?php
          $deals = [
            ["Ubud, Bali", "Rp 450.000", "ubud"],
            ["Jakarta", "Promo akhir pekan", "jakarta"],
            ["Yogyakarta", "Hotel + tiket hemat", "jogja"]
          ];
          foreach ($deals as $d): ?>
            <article class="card">
              <img src="https://picsum.photos/seed/<?= $d[2] ?>/600/400" alt="img">
              <h4><?= $d[0] ?></h4>
              <div class="muted"><?= $d[1] ?></div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Promo Aside -->
    <aside class="promo">
      <h3>Jadikan perjalananmu mudah</h3>
      <p>Cek penawaran harian, klaim voucher, dan gunakan pembayaran fleksibel.</p>
      <div class="pill">Gratis perubahan jadwal</div>

      <div style="margin-top:14px">
        <div style="display:flex;gap:8px;margin-top:12px">
          <div style="flex:1;background:#fff;padding:10px;border-radius:10px;text-align:center">
            <div style="font-weight:800">24k+</div>
            <div class="muted" style="font-size:12px;">Review</div>
          </div>
          <div style="flex:1;background:#fff;padding:10px;border-radius:10px;text-align:center">
            <div style="font-weight:800">99%</div>
            <div class="muted" style="font-size:12px;">Kepuasan</div>
          </div>
        </div>
      </div>
    </aside>
  </main>

  <!-- Rekomendasi -->
  <section class="section">
    <div style="display:flex;justify-content:space-between;align-items:end">
      <h3 style="font-size:16px">Rekomendasi untukmu</h3>
      <div class="muted">Dipilih berdasarkan pencarian</div>
    </div>

    <div class="cards-grid">
      <?php
      $rekom = [
        ["Paket Hemat Jakarta → Bandung", "Termasuk bus + hotel", 1],
        ["Diskon 20% Hotel Bali", "Periode terbatas", 2],
        ["Rute Internasional Murah", "Cek promo maskapai", 3],
      ];
      foreach ($rekom as $r): ?>
        <article class="card">
          <img src="https://picsum.photos/seed/<?= $r[2] ?>/600/400" alt="deal">
          <h4><?= $r[0] ?></h4>
          <div class="muted"><?= $r[1] ?></div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Fitur -->
  <div class="feature-strip section">
    <div class="feature">
      <strong>Harga Terbaik</strong>
      <div class="muted">Pembanding harga real-time</div>
    </div>
    <div class="feature">
      <strong>Pembayaran Mudah</strong>
      <div class="muted">Kartu, e-wallet, cicilan</div>
    </div>
    <div class="feature">
      <strong>Dukungan 24/7</strong>
      <div class="muted">Bantuan kapan saja</div>
    </div>
  </div>

  <footer>
    <div>© <strong>Travelly</strong> 2025</div>
    <div class="links">
      <div>Privasi</div>
      <div>Syarat</div>
      <div>Bantuan</div>
    </div>
  </footer>

</div>
</body>
</html>
