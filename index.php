<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user'])) {
    exit("403 Akses ditolak");
}

function tgl($date) {
    return $date ? date('d-m-Y', strtotime($date)) : '-';
}

$data = mysqli_query($conn, "
    SELECT 
        k.id_kunjungan,
        p.nama AS pasien,
        d.nama AS dokter,
        r.nama_ruangan,
        k.jenis_kunjungan,
        k.tanggal_kunjungan,
        k.status
    FROM kunjungan k
    JOIN pasien p ON k.id_pasien = p.id_pasien
    JOIN dokter d ON k.id_dokter = d.id_dokter
    LEFT JOIN ruangan r ON k.id_ruangan = r.id_ruangan
    ORDER BY k.tanggal_kunjungan DESC
");
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Data Kunjungan</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body class="container mt-4">

<h4>📅 Data Kunjungan</h4>

<div class="mb-3">
    <a href="../dashboard_front_office.php" class="btn btn-secondary">Kembali</a>
    <a href="tambah.php" class="btn btn-primary">+ Tambah Kunjungan</a>
</div>

<table class="table table-bordered table-striped align-middle">
<thead class="table-dark text-center">
<tr>
    <th>No</th>
    <th>Pasien</th>
    <th>Dokter</th>
    <th>Jenis</th>
    <th>Ruangan</th>
    <th>Tanggal</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($k=mysqli_fetch_assoc($data)): ?>
<tr>
    <td class="text-center"><?= $no++ ?></td>
    <td><?= $k['pasien'] ?></td>
    <td><?= $k['dokter'] ?></td>

    <!-- JENIS -->
    <td class="text-center">
        <?php if($k['jenis_kunjungan']=='rawat_inap'): ?>
            <span class="badge bg-danger">Rawat Inap</span>
        <?php else: ?>
            <span class="badge bg-success">Rawat Jalan</span>
        <?php endif; ?>
    </td>

    <!-- RUANGAN -->
    <td><?= $k['nama_ruangan'] ?? '-' ?></td>

    <!-- TANGGAL -->
    <td class="text-center"><?= tgl($k['tanggal_kunjungan']) ?></td>

    <!-- STATUS -->
    <td class="text-center">
        <?php
        switch($k['status']){
            case 'menunggu':
                echo "<span class='badge bg-warning'>Menunggu</span>";
                break;
            case 'diperiksa':
                echo "<span class='badge bg-info'>Diperiksa</span>";
                break;
            case 'dirawat':
                echo "<span class='badge bg-danger'>Dirawat</span>";
                break;
            case 'selesai':
                echo "<span class='badge bg-success'>Selesai</span>";
                break;
        }
        ?>
    </td>

    <!-- AKSI: Hanya Edit & Hapus -->
    <td class="text-center">
        <a href="edit.php?id=<?= $k['id_kunjungan'] ?>" 
           class="btn btn-warning btn-sm mb-1">Edit</a>

        <a href="simpan.php?hapus=<?= $k['id_kunjungan'] ?>" 
           onclick="return confirm('Yakin ingin hapus kunjungan ini?')" 
           class="btn btn-danger btn-sm">Hapus</a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</body>
</html>
