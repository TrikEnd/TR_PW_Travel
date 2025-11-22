<?php
// Jika ingin menambahkan proses PHP, bisa ditaruh di sini.
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Travelly — Pesan Tiket & Hotel</title>

<style>
/* ===== Reset & dasar ===== */
* { box-sizing: border-box; margin: 0; padding: 0; }

:root{
  --bg:#f6fbff;
  --accent:#1e90ff;
  --accent-2:#00a0ff;
  --muted:#6b7280;
  --card:#ffffff;
  --glass: rgba(255,255,255,0.55);
  --radius:12px;
  --shadow: 0 6px 18px rgba(16,24,40,0.08);
  font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

html,body {
  height:100%;
  background: linear-gradient(180deg,var(--bg),#eef7ff 60%);
  color:#0f172a;
}

/* ===== Container ===== */
.container{
  max-width:1150px;
  margin:28px auto;
  padding:0 20px;
}

/* ===== Header ===== */
header {
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:18px 0;
}

.brand {
  display:flex;
  align-items:center;
  gap:14px;
}

.logo {
  width:44px;
  height:44px;
  border-radius:10px;
  background: linear-gradient(135deg,var(--accent),var(--accent-2));
  display:flex;
  align-items:center;
  justify-content:center;
  color:white;
  font-weight:700;
  font-size:18px;
  box-shadow:var(--shadow);
}

nav {
  display:flex;
  gap:18px;
  align-items:center;
  color:var(--muted);
  font-weight:600;
}

.cta {
  padding:10px 14px;
  border-radius:10px;
  background:transparent;
  border:1px solid rgba(30,144,255,0.12);
  color:var(--accent);
  font-weight:700;
}

/* ===== Hero/Search Card ===== */
.hero {
  margin-top:18px;
  display:grid;
  grid-template-columns: 1fr 420px;
  gap:28px;
  align-items:start;
}

.hero-left {
  padding:28px;
  border-radius:18px;
  background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.82));
  box-shadow:var(--shadow);
}

h1 { font-size:28px; margin-bottom:8px; }
p.lead { color:var(--muted); margin-bottom:18px; }

/* Tabs */
.tabs { display:flex; gap:8px; margin-bottom:18px; }
.tab {
  padding:10px 12px;
  border-radius:12px;
  background:transparent;
  color:var(--muted);
  cursor:pointer;
  border:1px solid transparent;
  font-weight:700;
}
.tab.active {
  background:linear-gradient(90deg,var(--accent),var(--accent-2));
  color:white;
  box-shadow:0 8px 24px rgba(0,160,255,0.12);
}

/* Search form */
.search-row { display:flex; gap:12px; margin-bottom:12px; }
.field {
  flex:1;
  background:var(--card);
  padding:10px 12px;
  border-radius:12px;
  border:1px solid rgba(15,23,42,0.04);
}
.field.small { max-width:170px; }

.search-actions { display:flex; gap:12px; margin-top:8px; }

.btn-primary {
  padding:12px 16px;
  border-radius:12px;
  background:linear-gradient(90deg,var(--accent),var(--accent-2));
  color:white;
  border:none;
  font-weight:800;
  cursor:pointer;
  box-shadow:0 10px 30px rgba(30,144,255,0.12);
}
.btn-ghost {
  padding:12px 16px;
  border-radius:12px;
  background:transparent;
  border:1px solid rgba(15,23,42,0.06);
  cursor:pointer;
}

/* Right promo box */
.promo {
  padding:18px;
  border-radius:18px;
  background:linear-gradient(180deg,#ffffff30,#ffffff10);
  border:1px solid rgba(15,23,42,0.03);
  box-shadow:var(--shadow);
  backdrop-filter: blur(6px);
}

.promo h3 { margin-bottom:8px; font-size:16px; }
.promo p { color:var(--muted); font-size:14px; margin-bottom:12px; }
.promo .pill {
  display:inline-block;
  padding:8px 10px;
  border-radius:999px;
  background:rgba(0,160,255,0.08);
  color:var(--accent-2);
  font-weight:700;
  font-size:13px;
}

/* ===== Sections ===== */
.section { margin-top:26px; }

.cards-grid {
  display:grid;
  grid-template-columns: repeat(3,1fr);
  gap:18px;
  margin-top:14px;
}

.card {
  background:var(--card);
  border-radius:14px;
  padding:14px;
  box-shadow:var(--shadow);
  border:1px solid rgba(15,23,42,0.03);
}
.card img {
  width:100%;
  height:130px;
  object-fit:cover;
  border-radius:10px;
  margin-bottom:10px;
}
.card h4 { margin-bottom:6px; font-size:15px; }
.muted { color:var(--muted); font-size:13px; }

/* Features */
.feature-strip {
  margin-top:20px;
  display:flex;
  gap:12px;
  align-items:center;
  justify-content:space-between;
}
.feature {
  flex:1;
  background:linear-gradient(180deg,rgba(255,255,255,0.95),#fff);
  padding:14px;
  border-radius:12px;
  text-align:center;
  box-shadow:var(--shadow);
}
.feature strong {
  display:block;
  margin-bottom:6px;
  font-size:18px;
}

/* Footer */
footer {
  margin-top:36px;
  padding:26px 0;
  color:var(--muted);
  font-size:13px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
footer .links { display:flex; gap:14px; }

/* Responsive */
@media (max-width:980px){
  .hero{ grid-template-columns: 1fr; }
  .cards-grid { grid-template-columns: repeat(2,1fr); }
}
@media (max-width:620px){
  nav { display:none; }
  .cards-grid { grid-template-columns: 1fr; }
  .feature-strip{ flex-direction:column; }
  .search-row{ flex-direction:column; }
  .field.small{ max-width:100%; }
}
</style>
</head>

<body>
<div class="container">

<!-- Header -->
<header>
  <div class="brand">
    <div class="logo">Tr</div>
    <div>
      <div style="font-weight:800;">Travelly</div>
      <div style="font-size:12px;color:var(--muted)">Temukan perjalananmu</div>
    </div>
  </div>
  <nav>
    <div>Promo</div>
    <div>Pesanan</div>
    <div>Bantuan</div>
    <button class="cta">Masuk / Daftar</button>
  </nav>
</header>

<!-- Main -->
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
          <label style="font-size:12px;color:var(--muted);margin-bottom:6px">Keberangkatan</label>
          <input type="text" placeholder="Jakarta (CGK)" style="width:100%;border:0;outline:0;font-weight:700" />
        </div>
        <div class="field">
          <label style="font-size:12px;color:var(--muted);margin-bottom:6px">Tujuan</label>
          <input type="text" placeholder="Bali (DPS)" style="width:100%;border:0;outline:0;font-weight:700" />
        </div>
        <div class="field small">
          <label style="font-size:12px;color:var(--muted);margin-bottom:6px">Tanggal</label>
          <input type="text" placeholder="22 Nov - 26 Nov" style="width:100%;border:0;outline:0;font-weight:700" />
        </div>
        <div class="field small">
          <label s
