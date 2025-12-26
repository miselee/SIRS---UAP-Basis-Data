<?php
include "../config/database.php";

$id = (int)$_GET['id_dokter'];

$q = mysqli_query($conn,"
SELECT hari, jam_mulai, jam_selesai
FROM jadwal_dokter
WHERE id_dokter=$id
ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')
");

if(mysqli_num_rows($q)==0){
    echo "<div class='alert alert-warning'>Dokter belum memiliki jadwal</div>";
    exit;
}

echo "<ul class='list-group'>";
while($j=mysqli_fetch_assoc($q)){
    echo "
    <li class='list-group-item'>
        <strong>{$j['hari']}</strong><br>
        {$j['jam_mulai']} - {$j['jam_selesai']}
    </li>";
}
echo "</ul>";
