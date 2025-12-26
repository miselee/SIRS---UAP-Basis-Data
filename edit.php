<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'front_office') {
    echo "Akses ditolak";
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID pasien tidak ditemukan";
    exit;
}

$id = $_GET['id'];

$pasien = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM pasien WHERE id_pasien='$id'")
);

if (!$pasien) {
    echo "Data pasien tidak ditemukan";
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
        UPDATE pasien SET
            nama = '$nama',
            tanggal_lahir = " . ($tgl ? "'$tgl'" : "NULL") . ",
            jenis_kelamin = '$jk',
            alamat = '$alamat',
            no_telp = '$telp',
            bpjs = " . ($bpjs ? "'$bpjs'" : "NULL") . "
        WHERE id_pasien = '$id'
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
<title>Edit Pasien</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

<h4 class="mb-3"> Edit Data Pasien</h4>

<div class="card shadow-sm">
<div class="card-body">

<form method="post">

<div class="mb-3">
    <label class="form-label">Nama Pasien</label>
    <input type="text" name="nama" class="form-control"
           value="<?= $pasien['nama'] ?>" required>
</div>

<div class="mb-3">
    <label class="form-label">Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" class="form-control"
           value="<?= $pasien['tanggal_lahir'] ?>">
</div>

<div class="mb-3">
    <label class="form-label">Jenis Kelamin</label>
    <select name="jenis_kelamin" class="form-control" required>
        <option value="L" <?= $pasien['jenis_kelamin']=='L'?'selected':'' ?>>
            Laki-laki
        </option>
        <option value="P" <?= $pasien['jenis_kelamin']=='P'?'selected':'' ?>>
            Perempuan
        </option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Alamat</label>
    <textarea name="alamat" class="form-control" rows="3"><?= $pasien['alamat'] ?></textarea>
</div>

<div class="mb-3">
    <label class="form-label">No Telepon</label>
    <input type="text" name="no_telp" class="form-control"
           value="<?= $pasien['no_telp'] ?>">
</div>

<div class="mb-3">
    <label class="form-label">No BPJS (opsional)</label>
    <input type="text" name="bpjs" class="form-control"
           value="<?= $pasien['bpjs'] ?>">
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-warning">Update</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</div>
s
</form>

</div>
</div>

</div>

</body>
</html>
