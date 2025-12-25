<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user']) || (is_array($_SESSION['user']) && $_SESSION['user']['role'] !== 'dokter') || is_string($_SESSION['user'])) {
    // Jika hanya string disimpan, anggap string = username
    $username = is_string($_SESSION['user']) ? $_SESSION['user'] : ($_SESSION['user']['username'] ?? '');
    if (!$username) exit("403 Akses ditolak");
    $user_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM dokter WHERE username='$username'"));
    if (!$user_row) exit("403 Akses ditolak");
    $user = $user_row;
} else {
    $user = $_SESSION['user'];
}

$today = date('Y-m-d');
$id_dokter = $user['id_user'] ?? $user['id_dokter'] ?? 0;

$kunjungan = mysqli_query($conn, "
    SELECT k.id_kunjungan, p.nama AS pasien, k.jenis_kunjungan, k.status, k.jam_mulai
    FROM kunjungan k
    JOIN pasien p ON k.id_pasien = p.id_pasien
    WHERE k.id_dokter = $id_dokter AND k.tanggal_kunjungan='$today'
    ORDER BY k.jam_mulai ASC
");
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Dashboard Dokter</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Header Foto */
.header-foto {
    width: 100%;
    height: 180px;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 30px;
    position: relative;
}
.header-foto img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    filter: brightness(70%);
}

.header-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    text-align: center;
}
.header-text h2 {
    margin: 0;
    font-weight: bold;
    font-size: 1.8rem;
}
.header-text p {
    margin: 5px 0 0;
    font-size: 1rem;
}

.card-hover:hover {
    transform: translateY(-5px);
    transition: 0.3s;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
.card-icon {
    font-size: 2.5rem;
}

.list-kunjungan {
    max-height: 400px;
    overflow-y: auto;
    margin-top: 20px;
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

<nav class="navbar navbar-dark bg-success px-4">
    <span class="navbar-brand">👨‍⚕️ SIRS | Dokter</span>
    <span class="text-white">
        <?= htmlspecialchars($user['username'] ?? $user['nama']) ?> |
        <a href="auth/logout.php" class="text-white text-decoration-none"
        onclick="return confirm('Logout?')">Logout</a>
    </span>
</nav>

<div class="container mt-4">

    <!-- Header Foto -->
    <div class="header-foto shadow">
        <img src="assets/img/cover.png" alt="Header">
        <div class="header-text">
            <h2>Selamat Datang</h2>
            <p>Setiap senyum pasien adalah bukti pelayanan yang tulus</p>
        </div>
    </div>

    <h4 class="mb-4">Dashboard Dokter</h4>

    <div class="row g-4 mb-4">
        <!-- Kunjungan Pasien -->
        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center card-hover">
                <i class="bi bi-calendar-check card-icon text-primary"></i>
                <h6 class="mt-3">Kunjungan Pasien</h6>
                <a href="dokter/kunjungan_dokter/index.php" class="btn btn-primary btn-sm mt-2">Lihat</a>
            </div>
        </div>

        <!-- Rekam Medis -->
        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center card-hover">
                <i class="bi bi-journal-text card-icon text-warning"></i>
                <h6 class="mt-3">Rekam Medis</h6>
                <a href="dokter/rekam_medis/index.php" class="btn btn-warning btn-sm mt-2">Kelola</a>
            </div>
        </div>

        <!-- Gudang Farmasi -->
        <div class="col-md-4">
            <div class="card shadow-sm p-3 text-center card-hover">
                <i class="bi bi-box-seam card-icon text-danger"></i>
                <h6 class="mt-3">Gudang Farmasi</h6>
                <a href="dokter/farmasi/index.php" class="btn btn-danger btn-sm mt-2">Lihat</a>
            </div>
        </div>
    </div>

    <!-- List Kunjungan Hari Ini -->
    <div class="row g-4">
        <div class="col-lg-12">
            <h6>📋 Kunjungan Hari Ini (<?= date('d-m-Y') ?>)</h6>
            <ul class="list-group list-kunjungan mt-2">
                <?php if(mysqli_num_rows($kunjungan) > 0): ?>
                    <?php while($k = mysqli_fetch_assoc($kunjungan)): ?>
                        <li class="list-group-item">
                            <div>
                                <strong><?= htmlspecialchars($k['pasien']) ?></strong>
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

