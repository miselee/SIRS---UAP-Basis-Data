<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user'])) {
    exit("403 Akses ditolak");
}

if(!isset($_GET['id'])) {
    exit("ID kunjungan tidak ditemukan");
}

$id = (int)$_GET['id'];

$data = mysqli_query($conn, "SELECT * FROM kunjungan WHERE id_kunjungan=$id");
$kunjungan = mysqli_fetch_assoc($data);

if(!$kunjungan) {
    exit("Kunjungan tidak ditemukan");
}

$pasien = mysqli_query($conn, "SELECT * FROM pasien");
$dokter = mysqli_query($conn, "SELECT * FROM dokter");
$ruangan = mysqli_query($conn, "SELECT * FROM ruangan");
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Edit Kunjungan</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

<h4>Edit Kunjungan</h4>

<form action="simpan.php" method="post">
    <input type="hidden" name="id_kunjungan" value="<?= $kunjungan['id_kunjungan'] ?>">

    <div class="mb-3">
        <label>Pasien</label>
        <select name="id_pasien" class="form-control" required>
            <?php while($p=mysqli_fetch_assoc($pasien)): ?>
                <option value="<?= $p['id_pasien'] ?>" <?= $p['id_pasien']==$kunjungan['id_pasien']?'selected':'' ?>>
                    <?= $p['nama'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Dokter</label>
        <select name="id_dokter" class="form-control" required>
            <?php while($d=mysqli_fetch_assoc($dokter)): ?>
                <option value="<?= $d['id_dokter'] ?>" <?= $d['id_dokter']==$kunjungan['id_dokter']?'selected':'' ?>>
                    <?= $d['nama'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Jenis Kunjungan</label>
        <select name="jenis_kunjungan" class="form-control" required>
            <option value="rawat_jalan" <?= $kunjungan['jenis_kunjungan']=='rawat_jalan'?'selected':'' ?>>Rawat Jalan</option>
            <option value="rawat_inap" <?= $kunjungan['jenis_kunjungan']=='rawat_inap'?'selected':'' ?>>Rawat Inap</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Ruangan</label>
        <select name="id_ruangan" class="form-control">
            <option value="">- Pilih Ruangan -</option>
            <?php while($r=mysqli_fetch_assoc($ruangan)): ?>
                <option value="<?= $r['id_ruangan'] ?>" <?= $r['id_ruangan']==$kunjungan['id_ruangan']?'selected':'' ?>>
                    <?= $r['nama_ruangan'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Tanggal Kunjungan</label>
        <input type="date" name="tanggal_kunjungan" class="form-control" 
               value="<?= $kunjungan['tanggal_kunjungan'] ?>" required>
    </div>

    <div class="mb-3">
        <label>Jam Mulai</label>
        <input type="time" name="jam_mulai" class="form-control" value="<?= $kunjungan['jam_mulai'] ?>">
    </div>

    <div class="mb-3">
        <label>Jam Selesai</label>
        <input type="time" name="jam_selesai" class="form-control" value="<?= $kunjungan['jam_selesai'] ?>">
    </div>

    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="menunggu" <?= $kunjungan['status']=='menunggu'?'selected':'' ?>>Menunggu</option>
            <option value="diperiksa" <?= $kunjungan['status']=='diperiksa'?'selected':'' ?>>Diperiksa</option>
            <option value="dirawat" <?= $kunjungan['status']=='dirawat'?'selected':'' ?>>Dirawat</option>
            <option value="selesai" <?= $kunjungan['status']=='selesai'?'selected':'' ?>>Selesai</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

</body>
</html>
