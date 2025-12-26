<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'front_office') {
    exit("403 Akses ditolak");
}

$data = mysqli_query($conn, "
    SELECT 
        k.id_kunjungan,
        p.nama AS pasien,
        d.nama AS dokter,
        r.nama_ruangan,
        k.tanggal_kunjungan,
        k.status
    FROM kunjungan k
    JOIN pasien p ON k.id_pasien = p.id_pasien
    JOIN dokter d ON k.id_dokter = d.id_dokter
    JOIN ruangan r ON k.id_ruangan = r.id_ruangan
    WHERE k.jenis_kunjungan = 'rawat_inap'
    ORDER BY k.tanggal_kunjungan DESC
");
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Data Rawat Inap</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">
<h4>🏥 Data Rawat Inap</h4>

<div class="card shadow-sm mt-3">
<div class="card-body">

<table class="table table-bordered table-striped align-middle">
<thead class="table-dark text-center">
<tr>
    <th>No</th>
    <th>Nama Pasien</th>
    <th>Dokter</th>
    <th>Ruangan</th>
    <th>Tanggal Masuk</th>
    <th>Status</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($r=mysqli_fetch_assoc($data)): ?>
<tr>
    <td class="text-center"><?= $no++ ?></td>
    <td><?= $r['pasien'] ?></td>
    <td><?= $r['dokter'] ?></td>
    <td><?= $r['nama_ruangan'] ?></td>
    <td class="text-center"><?= date('d-m-Y', strtotime($r['tanggal_kunjungan'])) ?></td>
    <td class="text-center">
        <?php
        if($r['status']=='dirawat'){
            echo "<span class='badge bg-danger'>Dirawat</span>";
        } elseif($r['status']=='selesai'){
            echo "<span class='badge bg-success'>Pulang</span>";
        } else {
            echo "<span class='badge bg-warning'>Menunggu</span>";
        }
        ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>

</table>

</div>
</div>

<a href="../dashboard_front_office.php" class="btn btn-secondary mt-3">
← Kembali Dashboard
</a>

</div>

</body>
</html>
