<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'front_office') {
    exit("403 Akses ditolak");
}

$user = $_SESSION['user'];

$today = date('Y-m-d');
$kunjungan = mysqli_query($conn, "
    SELECT k.id_kunjungan, p.nama AS pasien, d.nama AS dokter, k.jenis_kunjungan, k.status
    FROM kunjungan k
    JOIN pasien p ON k.id_pasien = p.id_pasien
    JOIN dokter d ON k.id_dokter = d.id_dokter
    WHERE k.tanggal_kunjungan='$today'
    ORDER BY k.jam_mulai ASC
");
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Dashboard Front Office</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>

.header-foto {
    width: 100%;
    height: 180px;
    background-image: url('assets/img/header.jpg');
    background-size: cover;
    background-position: center;
    border-radius: 12px;
    margin-bottom: 30px;
}

.card-hover:hover {
    transform: translateY(-5px);
    transition: 0.3s;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}


.list-kunjungan {
    max-height: 400px;
    overflow-y: auto;
}

.list-group-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 1rem;
    border-radius: 8px;
    margin-bottom: 5px;
    background: #fff;
    border: 1px solid #ddd;
}

.badge-status {
    font-size: 0.8rem;
    padding: 0.4em 0.7em;
    border-radius: 0.5rem;
}
</style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-dark bg-primary px-4">
    <span class="navbar-brand">🏥 SIRS | Front Office</span>
    <span class="text-white">
        <a href="auth/logout.php" class="text-white text-decoration-none" onclick="return confirm('Logout?')">Logout</a>
    </span>
</nav>

<div class="container mt-4">
    <!-- Header Foto -->
    <div class="header-foto d-flex align-items-center justify-content-center text-white fs-3 fw-bold shadow">
        Selamat Datang di Dashboard Front Office
    </div>

    <!-- Cards Dashboard -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center card-hover">
                <i class="bi bi-people fs-1 text-success"></i>
                <h6 class="mt-2">Data Pasien</h6>
                <a href="pasien/index.php" class="btn btn-success btn-sm mt-2">Kelola</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center card-hover">
                <i class="bi bi-calendar-check fs-1 text-primary"></i>
                <h6 class="mt-2">Kunjungan</h6>
                <a href="kunjungan/index.php" class="btn btn-primary btn-sm mt-2">Kelola</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center card-hover">
                <i class="bi bi-hospital fs-1 text-danger"></i>
                <h6 class="mt-2">Rawat Inap</h6>
                <a href="rawat_inap/index.php" class="btn btn-danger btn-sm mt-2">Lihat Data</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center card-hover">
                <i class="bi bi-cash-stack fs-1 text-warning"></i>
                <h6 class="mt-2">Pembayaran</h6>
                <a href="pembayaran/index.php" class="btn btn-warning btn-sm mt-2">Proses</a>
            </div>
        </div>
    </div>

    <!-- Kunjungan Hari Ini -->
    <div class="row g-4">
        <div class="col-lg-12">
            <h6>📋 Kunjungan Hari Ini (<?= date('d-m-Y') ?>)</h6>
            <ul class="list-group list-kunjungan mt-2">
                <?php if(mysqli_num_rows($kunjungan) > 0): ?>
                    <?php while($k = mysqli_fetch_assoc($kunjungan)): ?>
                        <li class="list-group-item">
                            <div>
                                <strong><?= htmlspecialchars($k['pasien']) ?></strong> - <?= htmlspecialchars($k['dokter']) ?>
                                <span class="badge <?= $k['jenis_kunjungan']=='rawat_inap' ? 'bg-danger' : 'bg-success' ?> ms-2">
                                    <?= $k['jenis_kunjungan']=='rawat_inap' ? 'Rawat Inap' : 'Rawat Jalan' ?>
                                </span>
                            </div>
                            <span class="badge badge-status <?= $k['status']=='menunggu' ? 'bg-warning' : ($k['status']=='diperiksa' ? 'bg-info' : ($k['status']=='dirawat' ? 'bg-danger' : 'bg-success')) ?>">
                                <?= ucfirst($k['status']) ?>
                            </span>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="list-group-item text-muted">Belum ada kunjungan hari ini.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
