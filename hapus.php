<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'front_office') {
    echo "Akses ditolak";
    exit;
}

$id = $_GET['id'];


$cek = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM kunjungan WHERE id_pasien='$id'")
);

if ($cek > 0) {
    echo "<script>
        alert('Pasien sudah memiliki kunjungan, tidak bisa dihapus!');
        window.location='index.php';
    </script>";
    exit;
}

mysqli_query($conn, "DELETE FROM pasien WHERE id_pasien='$id'");
header("Location: index.php");
