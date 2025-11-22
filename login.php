<?php
    include 'koneksi.php'; // pastikan koneksi ke database sudah benar

    if (session_status() == PHP_SESSION_NONE) {
    session_start();

    if(isset($_POST['return_url'])){
    $_SESSION['return_to'] = $_POST['return_url'];
}
}
    // LOGIN

    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Cek user berdasarkan email
        $stmt = $conn->prepare("SELECT * FROM tb_user WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $redirect = isset($_SESSION['return_to']) ? $_SESSION['return_to'] : 'index.php';
            unset($_SESSION['return_to']);

            echo "<script>window.location.href = '$redirect';</script>";
            } else {
                echo "<script>alert('Password salah!');</script>";
            }
        } else {
            echo "<script>alert('Email tidak ditemukan!');</script>";
        }
    }

    // REGISTER
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];

        if ($password !== $password_confirm) {
            echo "<script>alert('Password dan konfirmasi password tidak sama!');</script>";
        } else {
            // Cek apakah email sudah terdaftar
            $stmt = $conn->prepare("SELECT * FROM tb_user WHERE email=? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<script>alert('Email sudah digunakan!');</script>";
            } else {
                // Hash password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO tb_user (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $password_hash);

                if ($stmt->execute()) {
                    echo "<script>alert('Register berhasil! Silahkan login.'); window.location.href='index.php';</script>";
                } else {
                    echo "<script>alert('Terjadi kesalahan saat register.');</script>";
                }
            }
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>

        <link rel="stylesheet" href="component/navbar.css">
        <link rel="stylesheet" href="login.css">
        <link rel="stylesheet" href="register.css">
    </head>
    <body>

        <?php include 'component/navbar.php'; ?>

        <div class="popup">

            <div class="popup-content">

                <form method="POST">
                    <h1>Login</h1>
        <input type="email" name="email" placeholder="example@gmail.com">
        <input type="password" name="password" placeholder="*">
        
        <label class="remember">
            <input type="checkbox"> Remember me
            <p> Dont have account?<b><a class="link" href="">Register</a></b></p>
        </label>
        <button type="submit" name="login">Login</button>
    </form>
            </div>
            </div>
        </div>

        <!-- REGISTER -->

        <div class="popup2">
        <div class="popup-content2">


            <form method="POST">
        <h1>Register</h1>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="example@gmail.com" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirm" placeholder="Password confirmation" required>
        
        <label class="remember2">
            <input type="checkbox"> Remember me
            <p>Have account? <b><a href="" class="link2">Login</a></b></p>
        </label>
        <button type="submit" name="register">Register</button>
        
    </form>
    

        </div>
    </div>

        <script>
    const popup = document.querySelector('.popup');
    const popup2 = document.querySelector('.popup2');
    const openBtn = document.getElementById('openPopup');   // Tombol buka Login
    const openBtn2 = document.getElementById('openRegis');  // Tombol buka Register

    // ✅ SIMPAN URL HALAMAN SEKARANG
    function saveReturnURL() {
        fetch('save_return_url.php', {
            method: 'POST',
            body: new URLSearchParams({
                return_url: window.location.href
            })
        });
    }

    // ✅ BUTTON CLOSE LOGIN
    const closePopup = document.createElement('div');
    closePopup.innerHTML = "&times;";
    closePopup.classList.add('close-popup');
    document.querySelector('.popup-content').appendChild(closePopup);

    // ✅ BUKA LOGIN
    openBtn.addEventListener('click', (e) => {
        e.preventDefault();
        saveReturnURL();
        popup.classList.add('show');
        popup2.classList.remove('show2');
    });

    // ✅ TUTUP LOGIN
    closePopup.addEventListener('click', () => {
        popup.classList.remove('show');
    });

    // ✅ KLIK DI LUAR LOGIN
    popup.addEventListener('click', (e) => {
        if (e.target === popup) popup.classList.remove('show');
    });

    // ✅ BUTTON CLOSE REGISTER
    const closePopup2 = document.createElement('div');
    closePopup2.innerHTML = "&times;";
    closePopup2.classList.add('close-popup2');
    document.querySelector('.popup-content2').appendChild(closePopup2);

    // ✅ BUKA REGISTER
    openBtn2.addEventListener('click', (e) => {
        e.preventDefault();
        saveReturnURL();
        popup2.classList.add('show2');
        popup.classList.remove('show');
    });

    // ✅ TUTUP REGISTER
    closePopup2.addEventListener('click', () => {
        popup2.classList.remove('show2');
    });

    // ✅ KLIK DI LUAR REGISTER
    popup2.addEventListener('click', (e) => {
        if (e.target === popup2) popup2.classList.remove('show2');
    });
</script>

    </body>


    </html>