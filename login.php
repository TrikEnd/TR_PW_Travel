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
            
            <h1>Login</h1>
            <input type="email" placeholder="example@Gmailcom">
            <input type="password" placeholder="*****">
        
        <label class="remember">
            <input type="checkbox"> Remember me

             <p> Dont have account?<b><a class="link" href="">Register</a></b></p>
        </label>

                      
            <button>Login</button>
        </div>
        </div>
    </div>

    <div class="popup2">
    <div class="popup-content2">

        <h1>Register</h1>

        <input type="text" placeholder="Username">
        <input type="email" placeholder="example@gmail.com">
        <input type="password" placeholder="Password">
        <input type="password" placeholder="Password confirmation">

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

