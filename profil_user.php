<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User - Travel Ticket</title>
    <link rel="stylesheet" href="component/navbar.css">
    <link rel="stylesheet" href="style/profil_user.css">
</head>
<body>
    <?php include 'component/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h1>Profil Saya</h1>
            <p class="subtitle">Kelola informasi profil Anda</p>
        </div>

        <div class="card">
            <h2>Informasi Profil</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="no_telepon" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Daftar</label>
                    <input type="text" value="01 Jan 2024" disabled>
                </div>
                <button type="submit" name="update_profile">Simpan Perubahan</button>
            </form>
        </div>

        <div class="card">
            <h2>Ubah Password</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Password Lama</label>
                    <input type="password" name="password_lama" required>
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="password_baru" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="konfirmasi_password" required>
                </div>
                <button type="submit" name="update_password">Update Password</button>
            </form>
        </div>

        <div class="card">
            <h2>Riwayat Pemesanan</h2>
            
            <div class="history-item">
                <h3><img src="asset/plane.png" alt="plane-icon">Garuda Indonesia - Ekonomi</h3>
                <p>Rumah Billie Eilish → Ngawi</p>
                <p><strong>Rp 3.000.000</strong> | 25 Dec 2024 | Kode: PSW001</p>
                <span class="status">Sudah Bayar</span>
            </div>

            <div class="history-item">
                <h3><img src="asset/train.png" alt="train-icon">Argo Parahyangan</h3>
                <p>Rumah Billie Eilish → Ngawi</p>
                <p><strong>Rp 300.000</strong> | 20 Dec 2024 | Kode: KRT001</p>
                <span class="status">Sudah Bayar</span>
            </div>

            <div class="history-item">
                <h3><img src="asset/bus.svg" alt="bus-icon">Bus Executive</h3>
                <p>Rumah Billie Eilish → Ngawi</p>
                <p><strong>Rp 200.000</strong> | 15 Dec 2024 | Kode: BUS001</p>
                <span class="status">Sudah Bayar</span>
            </div>
        </div>
    </div>
</body>
</html>

