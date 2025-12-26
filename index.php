<?php
session_start();
include "../config/database.php";

$data = mysqli_query($conn,"
    SELECT d.nama, d.spesialisasi, j.hari, j.jam_mulai, j.jam_selesai
    FROM jadwal_dokter j
    JOIN dokter d ON j.id_dokter = d.id_dokter
");
?>
<!doctype html>
<html>
<head>
<title>Jadwal Dokter</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h4>Jadwal Dokter</h4>
<table class="table table-bordered">
<tr>
<th>Dokter</th><th>Spesialis</th><th>Hari</th><th>Jam</th>
</tr>
<?php while($r=mysqli_fetch_assoc($data)): ?>
<tr>
<td><?= $r['nama'] ?></td>
<td><?= $r['spesialisasi'] ?></td>
<td><?= $r['hari'] ?></td>
<td><?= $r['jam_mulai'] ?> - <?= $r['jam_selesai'] ?></td>
</tr>
<?php endwhile; ?>
</table>
<a href="../dashboard.php" class="btn btn-secondary">Kembali</a>
</body>
</html>
