<?php
session_start();
include "../../config/database.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    exit("403 Akses ditolak");
}

$user = $_SESSION['user'];
$id_rekam_medis = isset($_GET['id_rekam_medis']) ? (int)$_GET['id_rekam_medis'] : 0;
if($id_rekam_medis <= 0) exit("Rekam medis tidak ditemukan.");

$sql = mysqli_query($conn, "
    SELECT rm.id_rekam_medis, rm.catatan, k.id_kunjungan, k.status AS status_kunjungan, p.nama, p.tanggal_lahir, p.jenis_kelamin
    FROM rekam_medis rm
    JOIN kunjungan k ON rm.id_kunjungan = k.id_kunjungan
    JOIN pasien p ON k.id_pasien = p.id_pasien
    WHERE rm.id_rekam_medis = $id_rekam_medis
");
if(!$rm = mysqli_fetch_assoc($sql)) exit("Rekam medis tidak ditemukan.");

$kode_master_js = [
    'A00' => 'Kolera',
    'A01' => 'Demam tifoid dan paratifoid',
    'A02' => 'Infeksi salmonella lainnya',
    'A03' => 'Shigellosis',
    'B01' => 'Cacar air',
    'C50' => 'Kanker payudara',
    'E11' => 'Diabetes melitus tipe 2',
    'I10' => 'Hipertensi esensial',
    'J18' => 'Pneumonia',
    'M54' => 'Nyeri punggung',
    'R05' => 'Batuk',
    'Z00' => 'Pemeriksaan kesehatan rutin',
    'Z23' => 'Vaksinasi',
    'UMUM' => 'Umum'
];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $kode = $_POST['kode_diagnosa'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';

    if($kode && $deskripsi){
        mysqli_query($conn, "
            INSERT INTO diagnosa (id_rekam_medis, kode_diagnosa, deskripsi)
            VALUES ($id_rekam_medis, '".mysqli_real_escape_string($conn,$kode)."', '".mysqli_real_escape_string($conn,$deskripsi)."')
        ");
        
        // Update status kunjungan otomatis jika masih menunggu
        if($rm['status_kunjungan'] == 'menunggu'){
            mysqli_query($conn, "UPDATE kunjungan SET status='selesai' WHERE id_kunjungan=".$rm['id_kunjungan']);
        }

        // Redirect ke resep setelah diagnosa
        echo "<script>window.location='resep.php?id_rekam_medis=$id_rekam_medis';</script>";
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Diagnosa Pasien</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script>
document.addEventListener('DOMContentLoaded', function(){
    const kodeSelect = document.getElementById('kode_diagnosa');
    const deskripsiField = document.getElementById('deskripsi');

    const kodeMaster = <?= json_encode($kode_master_js) ?>;

    kodeSelect.addEventListener('change', function(){
        const kode = this.value;
        if(kode && kodeMaster[kode]){
            deskripsiField.value = kodeMaster[kode];
        } else {
            deskripsiField.value = '';
        }
    });
});
</script>
</head>
<body class="container mt-4">
<h4>Diagnosa Pasien: <?= htmlspecialchars($rm['nama']) ?></h4>
<a href="index.php" class="btn btn-secondary btn-sm mb-3">← Kembali ke Rekam Medis</a>

<div class="card p-3">
    <p>Catatan: <?= htmlspecialchars($rm['catatan'] ?? '-') ?></p>

    <form method="post">
        <div class="mb-2">
            <label>Kode Diagnosa</label>
            <select name="kode_diagnosa" id="kode_diagnosa" class="form-select" required>
                <option value="">-- Pilih Kode --</option>
                <?php foreach($kode_master_js as $kode => $desc): ?>
                    <option value="<?= htmlspecialchars($kode) ?>"><?= htmlspecialchars($kode.' - '.$desc) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-2">
            <label>Deskripsi Diagnosa</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
        </div>
        <button class="btn btn-success">Simpan</button>
    </form>
</div>
</body>
</html>
