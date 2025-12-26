<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'dokter') {
    die("Akses ditolak");
}

if (!isset($_GET['id'])) {
    die("ID kunjungan tidak ditemukan");
}

$id = (int)$_GET['id']; 

$kunjungan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT k.id_kunjungan, p.nama AS pasien 
    FROM kunjungan k 
    JOIN pasien p ON k.id_pasien=p.id_pasien 
    WHERE k.id_kunjungan=$id
"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');
    $bb = isset($_POST['bb']) ? floatval($_POST['bb']) : null;
    $tb = isset($_POST['tb']) ? floatval($_POST['tb']) : null;
    $sistol = isset($_POST['sistol']) ? (int)$_POST['sistol'] : null;
    $diastol = isset($_POST['diastol']) ? (int)$_POST['diastol'] : null;
    $nadi = isset($_POST['nadi']) ? (int)$_POST['nadi'] : null;
    $suhu = isset($_POST['suhu']) ? floatval($_POST['suhu']) : null;
    $pernapasan = isset($_POST['pernapasan']) ? (int)$_POST['pernapasan'] : null;

    $stmt = $conn->prepare("
        INSERT INTO rekam_medis 
        (id_kunjungan, catatan, berat_badan, tinggi_badan, tekanan_darah_sistol, tekanan_darah_diastol, nadi, suhu, pernapasan) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isddiiidi", $id, $catatan, $bb, $tb, $sistol, $diastol, $nadi, $suhu, $pernapasan);
    $stmt->execute();
    $stmt->close();

    mysqli_query($conn, "UPDATE kunjungan SET status='selesai' WHERE id_kunjungan=$id");

    mysqli_query($conn, "UPDATE ruangan SET status='tersedia' WHERE id_ruangan=(SELECT id_ruangan FROM kunjungan WHERE id_kunjungan=$id)");

    header("Location: ../diagnosa/tambah.php?id=$id");
    exit;
}
?>

<!doctype html>
<html>
<head>
<title>Rekam Medis</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h4>Rekam Medis Pasien: <?= htmlspecialchars($kunjungan['pasien']) ?></h4>

<form method="POST">
    <div class="mb-3">
        <label>Catatan</label>
        <textarea name="catatan" class="form-control" required></textarea>
    </div>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label>Berat Badan (kg)</label>
            <input type="number" step="0.1" name="bb" class="form-control" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>Tinggi Badan (cm)</label>
            <input type="number" step="0.1" name="tb" class="form-control" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>Sistol (mmHg)</label>
            <input type="number" name="sistol" class="form-control" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>Diastol (mmHg)</label>
            <input type="number" name="diastol" class="form-control" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>Nadi (x/menit)</label>
            <input type="number" name="nadi" class="form-control" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>Suhu (°C)</label>
            <input type="number" step="0.1" name="suhu" class="form-control" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>Pernapasan (x/menit)</label>
            <input type="number" name="pernapasan" class="form-control" required>
        </div>
    </div>
    <button class="btn btn-primary">Simpan Rekam Medis</button>
</form>

</body>
</html>
