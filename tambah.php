<?php
include "../config/database.php";

if($_SESSION['user']['role']!='dokter'){
    die("Akses ditolak");
}


$id_rm = $_GET['rm'];
mysqli_query($conn,"INSERT INTO resep (id_rekam_medis) VALUES ($id_rm)");
$id_resep = mysqli_insert_id($conn);

if($_POST){
mysqli_query($conn,"
INSERT INTO resep_detail
(id_resep,nama_obat,dosis,aturan_pakai)
VALUES ($id_resep,'$_POST[obat]','$_POST[dosis]','$_POST[aturan]')
");
}
?>
