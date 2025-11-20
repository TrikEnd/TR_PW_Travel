<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil User - Travel Ticket</title>
    <link rel="stylesheet" href="style/profil_user.css">
    <link rel="stylesheet" href="component/navbar.css">
</head>
<body>
    <?php include 'component/navbar.php'; ?>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar"></div>
            <h1>Profil Saya</h1>
            <p>Kelola informasi profil Anda</p>
        </div>

        <div class="profile-content">
            <div class="tab-navigation">
                <button class="tab-btn active" onclick="openTab(event, 'info-tab')">Informasi Profil</button>
                <button class="tab-btn" onclick="openTab(event, 'security-tab')">Keamanan</button>
                <button class="tab-btn" onclick="openTab(event, 'history-tab')">Riwayat Pemesanan</button>
            </div>

            <div id="info-tab" class="tab-content active">
                <div class="card">
                    <h2>Edit Profil</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="no_telepon">No. Telepon</label>
                            <input type="text" id="no_telepon" name="no_telepon" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Tanggal Daftar</label>
                            <input type="text" disabled>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            <div id="security-tab" class="tab-content">
                <div class="card">
                    <h2>Ubah Password</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="password_lama">Password Lama</label>
                            <input type="password" id="password_lama" name="password_lama" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_baru">Password Baru</label>
                            <input type="password" id="password_baru" name="password_baru" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="konfirmasi_password">Konfirmasi Password</label>
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
                        </div>
                        
                        <button type="submit" name="update_password" class="btn-primary">Update Password</button>
                    </form>
                </div>
            </div>

            <div id="history-tab" class="tab-content">
                <div class="card">
                    <h2>Riwayat Pemesanan Pesawat</h2>
                    <div class="history-list">
                        <div class="history-item">
                            <div class="history-icon"></div>
                            <div class="history-details">
                                <h3>Garuda Indonesia - Ekonomi</h3>
                                <p>Rumah Billie Eilish → Ngawi</p>
                                <span class="date">25 Dec 2024</span>
                            </div>
                            <div class="history-status">
                                <span class="status sudah-bayar">Sudah Bayar</span>
                                <p class="price">Rp 3.000.000</p>
                                <span class="booking-code">PSW001</span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <h2>Riwayat Pemesanan Kereta</h2>
                    <div class="history-list">
                        <div class="history-item">
                            <div class="history-icon"></div>
                            <div class="history-details">
                                <h3>Argo Parahyangan</h3>
                                <p>Rumah Billie Eilish → Ngawi</p>
                                <span class="date">20 Dec 2024</span>
                            </div>
                            <div class="history-status">
                                <span class="status sudah-bayar">Sudah Bayar</span>
                                <p class="price">Rp 300.000</p>
                                <span class="booking-code">KRT001</span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <h2>Riwayat Pemesanan Bus</h2>
                    <div class="history-list">
                        <div class="history-item">
                            <div class="history-icon"></div>
                            <div class="history-details">
                                <h3>Bus Executive</h3>
                                <p>Rumah Billie Eilish → Ngawi</p>
                                <span class="date">15 Dec 2024</span>
                            </div>
                            <div class="history-status">
                                <span class="status sudah-bayar">Sudah Bayar</span>
                                <p class="price">Rp 200.000</p>
                                <span class="booking-code">BUS001</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            
            tablinks = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
    </script>
</body>
</html>
