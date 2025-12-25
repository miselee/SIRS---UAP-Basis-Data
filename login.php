<?php
session_start();
include "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

$q = mysqli_query($conn,"
SELECT * FROM users
WHERE username='$username'
AND password='$password'
AND status='aktif'
");

if (mysqli_num_rows($q) === 1) {

    $user = mysqli_fetch_assoc($q);
    $_SESSION['user'] = $user;

    if ($user['role'] === 'front_office') {
        header("Location: ../dashboard_front_office.php");
    } elseif ($user['role'] === 'dokter') {
        header("Location: ../dashboard_dokter.php");
    }
    exit;

} else {
    echo "<script>alert('Username / Password salah');history.back();</script>";
}
