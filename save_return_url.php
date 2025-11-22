<?php
session_start();
if (isset($_POST['return_url'])) {
    $_SESSION['return_to'] = $_POST['return_url'];
}
?>
