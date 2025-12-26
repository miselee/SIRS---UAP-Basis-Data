<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    exit("403 Akses ditolak");
}

$daftar_obat = [
    ["nama" => "Paracetamol", "stok" => 20, "satuan" => "tablet"],
    ["nama" => "Amoxicillin", "stok" => 8, "satuan" => "capsule"],
    ["nama" => "Ibuprofen", "stok" => 3, "satuan" => "tablet"],
    ["nama" => "Metformin", "stok" => 15, "satuan" => "tablet"],
    ["nama" => "Omeprazole", "stok" => 6, "satuan" => "capsule"],
    ["nama" => "Amlodipine", "stok" => 2, "satuan" => "tablet"],
    ["nama" => "Simvastatin", "stok" => 12, "satuan" => "tablet"],
    ["nama" => "Cefixime", "stok" => 5, "satuan" => "tablet"],
    ["nama" => "Cetirizine", "stok" => 10, "satuan" => "tablet"],
    ["nama" => "Salbutamol", "stok" => 4, "satuan" => "inhaler"]
];
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Daftar Obat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; }
.card-hover:hover {
    transform: translateY(-5px);
    transition: 0.3s;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.badge-habis { background-color: #dc3545; }
</style>
</head>
<body class="container mt-4">

<h4 class="mb-3">Daftar Obat</h4>

<div class="mb-3 d-flex gap-2">
    <input type="text" id="search" class="form-control" placeholder="Cari obat...">
    <a href="../../dashboard_dokter.php" class="btn btn-secondary">← Kembali</a>
</div>

<div class="row g-3" id="obat-container">
    <?php foreach($daftar_obat as $obat): ?>
        <div class="col-6 col-md-4 col-lg-3 obat-card">
            <div class="card p-3 card-hover h-100">
                <h6 class="card-title"><?= htmlspecialchars($obat['nama']) ?></h6>
                <p class="mb-1">Stok: 
                    <?= $obat['stok'] ?>
                    <?= $obat['stok'] <= 5 ? '<span class="badge badge-habis text-white">Hampir Habis</span>' : '' ?>
                </p>
                <p class="mb-0">Satuan: <?= htmlspecialchars($obat['satuan']) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.getElementById('search').addEventListener('input', function(){
    const filter = this.value.toLowerCase();
    document.querySelectorAll('.obat-card').forEach(card => {
        const name = card.querySelector('.card-title').textContent.toLowerCase();
        card.style.display = name.includes(filter) ? 'block' : 'none';
    });
});
</script>

</body>
</html>
