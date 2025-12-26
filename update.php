<?php
session_start();
include "../../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    exit("403 Akses ditolak");
}

$user = $_SESSION['user'];

$id_dokter = 0;
$res = mysqli_query($conn, "SELECT id_dokter FROM dokter WHERE user_id={$user['user_id']}");
if($row = mysqli_fetch_assoc($res)) $id_dokter = (int)$row['id_dokter'];

$id_pasien = isset($_GET['id_pasien']) ? (int)$_GET['id_pasien'] : 0;
if($id_pasien <= 0) exit("Pasien tidak ditemukan.");

$rm_res = mysqli_query($conn, "
    SELECT rm.*, k.id_kunjungan
    FROM rekam_medis rm
    JOIN kunjungan k ON rm.id_kunjungan = k.id_kunjungan
    WHERE k.id_pasien=$id_pasien AND k.id_dokter=$id_dokter
    ORDER BY rm.tanggal DESC
    LIMIT 1
");
$rm = mysqli_fetch_assoc($rm_res);

$pasien_res = mysqli_query($conn, "SELECT * FROM pasien WHERE id_pasien=$id_pasien");
$pasien = mysqli_fetch_assoc($pasien_res);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_kunjungan = (int)$_POST['id_kunjungan'];
    $berat = $_POST['berat_badan'] ?? null;
    $tinggi = $_POST['tinggi_badan'] ?? null;
    $sistol = $_POST['tekanan_sistol'] ?? null;
    $diastol = $_POST['tekanan_diastol'] ?? null;
    $nadi = $_POST['nadi'] ?? null;
    $suhu = $_POST['suhu'] ?? null;
    $pernapasan = $_POST['pernapasan'] ?? null;
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');

    if($rm){ // update existing
        mysqli_query($conn, "
            UPDATE rekam_medis SET 
                berat_badan='$berat',
                tinggi_badan='$tinggi',
                tekanan_darah_sistol='$sistol',
                tekanan_darah_diastol='$diastol',
                nadi='$nadi',
                suhu='$suhu',
                pernapasan='$pernapasan',
                catatan='$catatan'
            WHERE id_rekam_medis=".$rm['id_rekam_medis']."
        ");
    } else { // insert baru
        mysqli_query($conn, "
            INSERT INTO rekam_medis 
            (id_kunjungan, berat_badan, tinggi_badan, tekanan_darah_sistol, tekanan_darah_diastol, nadi, suhu, pernapasan, catatan)
            VALUES 
            ($id_kunjungan, '$berat', '$tinggi', '$sistol', '$diastol', '$nadi', '$suhu', '$pernapasan', '$catatan')
        ");
    }

    echo "<script>alert('Rekam medis disimpan');window.location='index.php';</script>";
    exit;
}

$id_kunjungan = $rm['id_kunjungan'] ?? null;
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Update Rekam Medis</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h4>Update Rekam Medis: <?= htmlspecialchars($pasien['nama']) ?></h4>
<a href="index.php" class="btn btn-secondary btn-sm mb-3">← Kembali</a>

<?php if(!$id_kunjungan): ?>
    <div class="alert alert-warning">Belum ada kunjungan untuk pasien ini.</div>
<?php else: ?>
<form method="post">
    <input type="hidden" name="id_kunjungan" value="<?= $id_kunjungan ?>">
    <div class="mb-2">
        <label>Berat Badan (kg)</label>
        <input type="number" step="0.1" name="berat_badan" class="form-control" value="<?= $rm['berat_badan'] ?? '' ?>" required>
    </div>
    <div class="mb-2">
        <label>Tinggi Badan (cm)</label>
        <input type="number" step="0.1" name="tinggi_badan" class="form-control" value="<?= $rm['tinggi_badan'] ?? '' ?>" required>
    </div>
    <div class="mb-2">
        <label>Tekanan Darah (sistol/diastol)</label>
        <div class="d-flex gap-2">
            <input type="number" name="tekanan_sistol" class="form-control" placeholder="Sistol" value="<?= $rm['tekanan_darah_sistol'] ?? '' ?>" required>
            <input type="number" name="tekanan_diastol" class="form-control" placeholder="Diastol" value="<?= $rm['tekanan_darah_diastol'] ?? '' ?>" required>
        </div>
    </div>
    <div class="mb-2">
        <label>Nadi (bpm)</label>
        <input type="number" name="nadi" class="form-control" value="<?= $rm['nadi'] ?? '' ?>" required>
    </div>
    <div class="mb-2">
        <label>Suhu (°C)</label>
        <input type="number" step="0.1" name="suhu" class="form-control" value="<?= $rm['suhu'] ?? '' ?>" required>
    </div>
    <div class="mb-2">
        <label>Pernapasan (x/menit)</label>
        <input type="number" name="pernapasan" class="form-control" value="<?= $rm['pernapasan'] ?? '' ?>" required>
    </div>
    <div class="mb-2">
        <label>Catatan</label>
        <textarea name="catatan" class="form-control" rows="3"><?= $rm['catatan'] ?? '' ?></textarea>
    </div>
    <button class="btn btn-success">Simpan</button>
</form>
<?php endif; ?>
</body>
</html>
