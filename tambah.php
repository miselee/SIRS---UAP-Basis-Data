<?php
session_start();
include "../config/database.php";


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'front_office') {
    echo "Akses ditolak";
    exit;
}

$user_id = $_SESSION['user']['user_id'];

$fo = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT id_front_office FROM front_office WHERE user_id='$user_id'")
);

if (!$fo) {
    echo "Front office tidak ditemukan";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama   = $_POST['nama'];
    $jk     = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $telp   = $_POST['no_telp'];

    $tgl  = !empty($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : NULL;
    $bpjs = !empty($_POST['bpjs']) ? $_POST['bpjs'] : NULL;

    $sql = "
        INSERT INTO pasien
        (nama, tanggal_lahir, jenis_kelamin, alamat, no_telp, bpjs, id_front_office)
        VALUES
        (
            '$nama',
            " . ($tgl ? "'$tgl'" : "NULL") . ",
            '$jk',
            '$alamat',
            '$telp',
            " . ($bpjs ? "'$bpjs'" : "NULL") . ",
            {$fo['id_front_office']}
        )
    ";

    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Tambah Pasien</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<h4 class="mb-3">Tambah Pasien</h4>

<div class="card shadow-sm">
<div class="card-body">

<form method="post">

<div class="mb-3">
    <label class="form-label">Nama Pasien</label>
    <input type="text" name="nama" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Jenis Kelamin</label>
    <select name="jenis_kelamin" class="form-control" required>
        <option value="">-- Pilih --</option>
        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Alamat</label>
    <textarea name="alamat" class="form-control" rows="3"></textarea>
</div>

<div class="mb-3">
    <label class="form-label">No Telepon</label>
    <input type="text" name="no_telp" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">No BPJS (opsional)</label>
    <input type="text" name="bpjs" class="form-control">
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</div>

</form>

</div>
</div>

</div>

</body>
</html>
