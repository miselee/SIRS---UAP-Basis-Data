<?php
session_start();
include "../../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    exit("403 Akses ditolak");
}

$user = $_SESSION['user'];

$id_kunjungan = isset($_GET['id_kunjungan']) ? (int)$_GET['id_kunjungan'] : 0;
if($id_kunjungan <= 0) exit("Kunjungan tidak ditemukan.");

$sql = mysqli_query($conn, "
    SELECT k.id_kunjungan, k.jenis_kunjungan, p.nama, p.tanggal_lahir, p.jenis_kelamin
    FROM kunjungan k
    JOIN pasien p ON k.id_pasien = p.id_pasien
    WHERE k.id_kunjungan = $id_kunjungan
");
if(!$kunjungan = mysqli_fetch_assoc($sql)) exit("Kunjungan tidak ditemukan.");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $berat = $_POST['berat_badan'] ?? null;
    $tinggi = $_POST['tinggi_badan'] ?? null;
    $sistol = $_POST['tekanan_sistol'] ?? null;
    $diastol = $_POST['tekanan_diastol'] ?? null;
    $nadi = $_POST['nadi'] ?? null;
    $suhu = $_POST['suhu'] ?? null;
    $pernapasan = $_POST['pernapasan'] ?? null;
    $catatan = $_POST['catatan'] ?? '';
    
    $cek = mysqli_query($conn, "SELECT id_rekam_medis FROM rekam_medis WHERE id_kunjungan=$id_kunjungan");
    if(mysqli_num_rows($cek) > 0){
        $rm = mysqli_fetch_assoc($cek);
        mysqli_query($conn, "
            UPDATE rekam_medis SET 
                berat_badan='$berat', tinggi_badan='$tinggi', tekanan_darah_sistol='$sistol', 
                tekanan_darah_diastol='$diastol', nadi='$nadi', suhu='$suhu', pernapasan='$pernapasan',
                catatan='".mysqli_real_escape_string($conn,$catatan)."'
            WHERE id_rekam_medis=".$rm['id_rekam_medis']."
        ");
        $id_rekam_medis = $rm['id_rekam_medis'];
    } else {
        mysqli_query($conn, "
            INSERT INTO rekam_medis (id_kunjungan, catatan, berat_badan, tinggi_badan, tekanan_darah_sistol, tekanan_darah_diastol, nadi, suhu, pernapasan)
            VALUES ($id_kunjungan, '".mysqli_real_escape_string($conn,$catatan)."', '$berat', '$tinggi', '$sistol', '$diastol', '$nadi', '$suhu', '$pernapasan')
        ");
        $id_rekam_medis = mysqli_insert_id($conn);
    }

    header("Location: diagnosa.php?id_rekam_medis=$id_rekam_medis");
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Form Rekam Medis</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h4>Form Rekam Medis</h4>
<a href="index.php" class="btn btn-secondary btn-sm mb-3">← Kembali ke Kunjungan</a>

<div class="card p-3">
    <h5><?= htmlspecialchars($kunjungan['nama']) ?> (<?= $kunjungan['jenis_kelamin']=='L'?'Laki-laki':'Perempuan' ?>)</h5>
    <p>Tanggal Lahir: <?= $kunjungan['tanggal_lahir'] ? date('d-m-Y', strtotime($kunjungan['tanggal_lahir'])) : '-' ?><br>
       Jenis Kunjungan: <?= $kunjungan['jenis_kunjungan']=='rawat_inap'?'Rawat Inap':'Rawat Jalan' ?></p>

    <form method="post">
        <div class="mb-2">
            <label>Berat Badan (kg)</label>
            <input type="number" step="0.1" name="berat_badan" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Tinggi Badan (cm)</label>
            <input type="number" step="0.1" name="tinggi_badan" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Tekanan Darah (sistol/diastol)</label>
            <div class="d-flex gap-2">
                <input type="number" name="tekanan_sistol" class="form-control" placeholder="Sistol" required>
                <input type="number" name="tekanan_diastol" class="form-control" placeholder="Diastol" required>
            </div>
        </div>
        <div class="mb-2">
            <label>Nadi (bpm)</label>
            <input type="number" name="nadi" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Suhu (°C)</label>
            <input type="number" step="0.1" name="suhu" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Pernapasan (x/menit)</label>
            <input type="number" name="pernapasan" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Catatan</label>
            <textarea name="catatan" class="form-control" rows="3"></textarea>
        </div>
        <button class="btn btn-success">Simpan & Lanjut Diagnosa</button>
    </form>
</div>
</body>
</html>
