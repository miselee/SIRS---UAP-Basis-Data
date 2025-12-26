<?php
include "../config/database.php";

$id = (int)$_GET['id_dokter'];
$q = mysqli_query($conn,"SELECT DISTINCT hari FROM jadwal_dokter WHERE id_dokter=$id");

$hari=[];
while($r=mysqli_fetch_assoc($q)){
    $hari[]=$r['hari'];
}
echo json_encode($hari);
