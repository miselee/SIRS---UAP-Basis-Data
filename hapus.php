<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'front_office') {
    exit("403 Akses ditolak");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id > 0){
    mysqli_query($conn, "DELETE FROM pembayaran WHERE id_pembayaran=$id");
}

header("Location: index.php");
exit;
