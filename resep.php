<?php
session_start();
include "../../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    exit("403 Akses ditolak");
}

$id_rekam_medis = isset($_GET['id_rekam_medis']) ? (int)$_GET['id_rekam_medis'] : 0;
if($id_rekam_medis <= 0) exit("Rekam medis tidak ditemukan.");

$daftar_obat = [
    "Paracetamol", "Amoxicillin", "Ibuprofen", "Metformin", "Omeprazole",
    "Amlodipine", "Simvastatin", "Cefixime", "Cetirizine", "Salbutamol"
];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    mysqli_query($conn, "INSERT INTO resep (id_rekam_medis) VALUES ($id_rekam_medis)");
    $id_resep = mysqli_insert_id($conn);

    foreach($_POST['nama_obat'] as $i => $obat){
        $dosis = $_POST['dosis'][$i] ?? '';
        $aturan = $_POST['aturan'][$i] ?? '';
        mysqli_query($conn, "
            INSERT INTO resep_detail (id_resep, nama_obat, dosis, aturan_pakai)
            VALUES ($id_resep, '".mysqli_real_escape_string($conn,$obat)."',
            '".mysqli_real_escape_string($conn,$dosis)."',
            '".mysqli_real_escape_string($conn,$aturan)."')
        ");
    }
    echo "<script>alert('Resep disimpan');window.location='index.php';</script>";
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Buat Resep</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h4>Buat Resep Pasien</h4>
<a href="index.php" class="btn btn-secondary btn-sm mb-3">← Kembali</a>

<form method="post">
    <div id="obat-container">
        <div class="mb-3 row g-2 obat-item">
            <div class="col-md-4">
                <select name="nama_obat[]" class="form-select" required>
                    <option value="">-- Pilih Obat --</option>
                    <?php foreach($daftar_obat as $o): ?>
                        <option value="<?= htmlspecialchars($o) ?>"><?= htmlspecialchars($o) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="dosis[]" class="form-control" placeholder="Dosis" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="aturan[]" class="form-control" placeholder="Aturan Pakai" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-remove">×</button>
            </div>
        </div>
    </div>
    <button type="button" id="add-obat" class="btn btn-info btn-sm mb-3">Tambah Obat</button>
    <br>
    <button class="btn btn-success">Simpan Resep</button>
</form>

<script>
document.getElementById('add-obat').addEventListener('click', function(){
    const container = document.getElementById('obat-container');
    const item = container.querySelector('.obat-item').cloneNode(true);
    item.querySelectorAll('input, select').forEach(e => e.value = '');
    container.appendChild(item);
});

document.addEventListener('click', function(e){
    if(e.target.classList.contains('btn-remove')){
        const items = document.querySelectorAll('.obat-item');
        if(items.length > 1) e.target.closest('.obat-item').remove();
    }
});
</script>
</body>
</html>
