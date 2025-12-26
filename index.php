<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'front_office') {
    exit("403 Akses ditolak");
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$data = mysqli_query($conn, "
    SELECT p.id_pembayaran, k.id_kunjungan, pa.nama AS pasien, pa.bpjs, k.jenis_kunjungan, p.total, p.metode, p.status, p.tanggal
    FROM pembayaran p
    JOIN kunjungan k ON p.id_kunjungan = k.id_kunjungan
    JOIN pasien pa ON k.id_pasien = pa.id_pasien
    " . ($search ? " WHERE pa.nama LIKE '%$search%'" : "") . "
    ORDER BY p.tanggal DESC
");
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Pembayaran</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f5f5f5; }
.table-modern th, .table-modern td { vertical-align: middle; }
.badge-status { font-size: 0.85rem; padding: 0.4em 0.7em; border-radius: 0.5rem; }
.card-hover:hover { transform: translateY(-5px); transition: 0.3s; box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
</style>
</head>
<body class="container mt-4">

<h4>💳 Pembayaran</h4>
<div class="mb-3 d-flex gap-2">
    <form method="get" class="flex-grow-1 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Cari pasien..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
    </form>
    <a href="../dashboard_front_office.php" class="btn btn-secondary">← Kembali</a>
</div>

<div class="card p-3 shadow-sm card-hover">
    <table class="table table-striped table-bordered table-modern">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>Pasien</th>
                <th>Jenis Kunjungan</th>
                <th>Total</th>
                <th>Metode</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['pasien']) ?></td>
                <td class="text-center"><?= $row['jenis_kunjungan']=='rawat_inap'?'Rawat Inap':'Rawat Jalan' ?></td>
                <td class="text-end">Rp <?= number_format($row['total'],0,',','.') ?></td>
                <td class="text-center"><?= $row['metode'] ?></td>
                <td class="text-center">
                    <?php
                        switch($row['status']){
                            case 'pending': echo "<span class='badge bg-warning badge-status'>Pending</span>"; break;
                            case 'lunas': echo "<span class='badge bg-success badge-status'>Lunas</span>"; break;
                            case 'batal': echo "<span class='badge bg-danger badge-status'>Batal</span>"; break;
                        }
                    ?>
                </td>
                <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                <td class="text-center">
                    <a href="edit.php?id=<?= $row['id_pembayaran'] ?>" class="btn btn-warning btn-sm mb-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                    <a href="hapus.php?id=<?= $row['id_pembayaran'] ?>" onclick="return confirm('Yakin ingin hapus pembayaran?')" class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
