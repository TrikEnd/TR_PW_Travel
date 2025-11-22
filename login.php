<?php
include 'koneksi.php'; // pastikan koneksi ke database sudah benar

session_start();

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
            echo "<script>alert('Login berhasil!'); window.location.href='index.php';</script>";
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
    <input type="password" name="password" placeholder="*****">
    <button type="submit" name="login">Login</button>
</form>

<label class="remember">
    <input type="checkbox"> Remember me

     <p> Dont have account?<b><a class="link" href="">Register</a></b></p>
</label>
        </div>
        </div>
    </div>

    <!-- REGISTER -->

    <div class="popup2">
    <div class="popup-content2">

        <h1>Register</h1>

        <form method="POST">
    <h1>Register</h1>
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="example@gmail.com" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="password_confirm" placeholder="Password confirmation" required>
    <button type="submit" name="register">Register</button>
</form>

        <label class="remember2">
            <input type="checkbox"> Remember me
            <p>Have account? <b><a href="" class="link2">Login</a></b></p>
        </label>

        <button>Register</button>

    </div>
</div>

    <script>
    const popup = document.querySelector('.popup');
const popup2 = document.querySelector('.popup2');
const openBtn = document.getElementById('openPopup');

// Close button Login
const closePopup = document.createElement('div');
closePopup.innerHTML = "&times;";
closePopup.classList.add('close-popup');
document.querySelector('.popup-content').appendChild(closePopup);

// Open Login
openBtn.addEventListener('click', (e) => {
    e.preventDefault();
    popup.classList.add('show');
    popup2.classList.remove('show2'); // ← Tutup Register
});

// Close Login
closePopup.addEventListener('click', () => {
    popup.classList.remove('show');
});

// Click outside
popup.addEventListener('click', (e) => {
    if (e.target === popup) popup.classList.remove('show');
});
 
</script>

<script>
    const openBtn2 = document.getElementById('openRegis');

// Close button Register
const closePopup2 = document.createElement('div');
closePopup2.innerHTML = "&times;";
closePopup2.classList.add('close-popup2');
document.querySelector('.popup-content2').appendChild(closePopup2);

// Open Register
openBtn2.addEventListener('click', (e) => {
    e.preventDefault();
    popup2.classList.add('show2');
    popup.classList.remove('show'); // ← Tutup Login
});

// Close Register
closePopup2.addEventListener('click', () => {
    popup2.classList.remove('show2');
});

// Click outside
popup2.addEventListener('click', (e) => {
    if (e.target === popup2) popup2.classList.remove('show2');
});
;
    </script>
</body>


</html>

