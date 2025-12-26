<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'front_office') {
    echo "Akses ditolak";
    exit;
}

$data = mysqli_query($conn, "
    SELECT p.*, f.nama AS nama_fo
    FROM pasien p
    LEFT JOIN front_office f ON p.id_front_office = f.id_front_office
    ORDER BY p.dibuat_pada DESC
");
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Data Pasien</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>📋 Data Pasien</h4>
    <a href="tambah.php" class="btn btn-primary">+ Tambah Pasien</a>
</div>

<div class="card shadow-sm">
<div class="card-body">

<table class="table table-striped table-bordered align-middle">
<thead class="table-dark text-center">
<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Tgl Lahir</th>
    <th>JK</th>
    <th>No Telp</th>
    <th>BPJS</th>
    <th>Dibuat</th>
    <th>Petugas FO</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($p=mysqli_fetch_assoc($data)): ?>
<tr>
    <td class="text-center"><?= $no++ ?></td>
    <td><?= $p['nama'] ?></td>
    <td class="text-center">
        <?= $p['tanggal_lahir'] ?? '-' ?>
    </td>
    <td class="text-center">
        <?= $p['jenis_kelamin']=='L' ? 'Laki-laki' : 'Perempuan' ?>
    </td>
    <td><?= $p['no_telp'] ?? '-' ?></td>
    <td class="text-center">
        <?= $p['bpjs'] ? $p['bpjs'] : '<span class="badge bg-secondary">Non BPJS</span>' ?>
    </td>
    <td class="text-center">
        <?= date('d-m-Y', strtotime($p['dibuat_pada'])) ?>
    </td>
    <td><?= $p['nama_fo'] ?? '-' ?></td>
    <td class="text-center">
        <a href="edit.php?id=<?= $p['id_pasien'] ?>" class="btn btn-warning btn-sm">Edit</a>
        <a href="hapus.php?id=<?= $p['id_pasien'] ?>" 
           class="btn btn-danger btn-sm"
           onclick="return confirm('Yakin hapus data pasien?')">
           Hapus
        </a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>

</table>

</div>
</div>

<a href="../dashboard_front_office.php" class="btn btn-secondary mt-3">← Kembali Dashboard</a>

</div>

</body>
</html>
