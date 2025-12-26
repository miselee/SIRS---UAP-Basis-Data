<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'front_office') {
    exit("403 Akses ditolak");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0) exit("Pembayaran tidak ditemukan.");

$stmt = $conn->prepare("
    SELECT p.*, k.id_kunjungan, pa.nama AS pasien, pa.bpjs
    FROM pembayaran p
    JOIN kunjungan k ON p.id_kunjungan = k.id_kunjungan
    JOIN pasien pa ON k.id_pasien = pa.id_pasien
    WHERE p.id_pembayaran=?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows==0) exit("Pembayaran tidak ditemukan.");
$row = $result->fetch_assoc();
$stmt->close();

$enum_result = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'status'");
$enum_row = $enum_result->fetch_assoc();
preg_match("/^enum\('(.*)'\)$/", $enum_row['Type'], $matches);
$valid_status = explode("','", $matches[1]);

if($_SERVER['REQUEST_METHOD']==='POST'){
    $total = (float)$_POST['total'];
    $status_input = $_POST['status'] ?? $valid_status[0];
    $metode_input = $_POST['metode'] ?? 'Tunai';

    if(!in_array($status_input, $valid_status)) $status_input = $valid_status[0];

    $stmt = $conn->prepare("UPDATE pembayaran SET total=?, status=?, metode=? WHERE id_pembayaran=?");
    $stmt->bind_param("dssi", $total, $status_input, $metode_input, $id); // <--- fix di sini
    if(!$stmt->execute()) die("Gagal update: ".$stmt->error);
    $stmt->close();

    echo "<script>alert('Pembayaran berhasil diperbarui');window.location='index.php';</script>";
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Edit Pembayaran</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h4>Edit Pembayaran</h4>
<a href="index.php" class="btn btn-secondary btn-sm mb-3">← Kembali</a>

<div class="card p-3 shadow-sm">
    <p><strong>Pasien:</strong> <?= htmlspecialchars($row['pasien']) ?></p>
    <form method="post">
        <div class="mb-2">
            <label>Total (Rp)</label>
            <input type="number" step="0.01" name="total" class="form-control" required value="<?= htmlspecialchars($row['total']) ?>">
        </div>

        <div class="mb-2">
            <label>Metode Pembayaran</label>
            <select name="metode" class="form-select" required>
                <option value="Tunai" <?= $row['metode']=='Tunai'?'selected':'' ?>>Tunai</option>
                <?php if(!empty($row['bpjs'])): ?>
                <option value="BPJS" <?= $row['metode']=='BPJS'?'selected':'' ?>>BPJS</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="mb-2">
            <label>Status</label>
            <select name="status" class="form-select" required>
                <?php foreach($valid_status as $s): ?>
                    <option value="<?= $s ?>" <?= $row['status']==$s?'selected':'' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn btn-success">Simpan</button>
    </form>
</div>

</body>
</html>
