<?php
session_start();
include "../config/database.php";

$pasien  = mysqli_query($conn,"SELECT * FROM pasien");  
$ruangan = mysqli_query($conn,"SELECT * FROM ruangan WHERE status='tersedia'");
$dokter  = mysqli_query($conn,"SELECT * FROM dokter");
?>

<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Tambah Kunjungan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container mt-4">
<h4 class="mb-3">Tambah Kunjungan</h4>

<div class="row g-4">

<!-- FORM KUNJUNGAN -->
<div class="col-md-7">
<form method="POST" action="simpan.php">

<label>Pasien</label>
<select name="id_pasien" class="form-control mb-2" required>
<option value="">-- Pilih --</option>
<?php while($p=mysqli_fetch_assoc($pasien)): ?>
<option value="<?= $p['id_pasien'] ?>"><?= $p['nama'] ?></option>
<?php endwhile; ?>
</select>

<label>Jenis Kunjungan</label>
<select name="jenis_kunjungan" class="form-control mb-2" required>
<option value="">-- Pilih --</option>
<option value="rawat_jalan">Rawat Jalan</option>
<option value="rawat_inap">Rawat Inap</option>
</select>

<label>Dokter</label>
<select name="id_dokter" id="dokter" class="form-control mb-2" required>
<option value="">-- Pilih Dokter --</option>
<?php while($d=mysqli_fetch_assoc($dokter)): ?>
<option value="<?= $d['id_dokter'] ?>"><?= $d['nama'] ?> (<?= $d['spesialisasi'] ?>)</option>
<?php endwhile; ?>
</select>

<label>Ruangan</label>
<select name="id_ruangan" id="ruangan" class="form-control mb-2" required>
<option value="">-- Pilih Ruangan --</option>
<?php while($r=mysqli_fetch_assoc($ruangan)): ?>
<option value="<?= $r['id_ruangan'] ?>"><?= $r['nama_ruangan'] ?></option>
<?php endwhile; ?>
</select>

<label>Tanggal</label>
<input type="date" name="tanggal" id="tanggal" class="form-control mb-2" required>

<!-- Jam dihilangkan atau optional -->
<label>Jam Mulai (opsional)</label>
<input type="time" name="jam_mulai" id="jam_mulai" class="form-control mb-2">

<label>Jam Selesai (opsional)</label>
<input type="time" name="jam_selesai" id="jam_selesai" class="form-control mb-3">

<button class="btn btn-primary">Simpan</button>
<a href="index.php" class="btn btn-secondary">Kembali</a>
</form>
</div>

<!-- JADWAL DOKTER -->
<div class="col-md-5">
<div class="card shadow-sm">
<div class="card-header bg-info text-white">
Jadwal Dokter
</div>
<div class="card-body" id="jadwal">
<p class="text-muted">Pilih dokter untuk melihat jadwal</p>
</div>
</div>
</div>

</div>
</div>

<script>
const dokterSelect = document.getElementById('dokter');
const tanggalInput = document.getElementById('tanggal');
const jadwalDiv = document.getElementById('jadwal');
let hariValid = [];

dokterSelect.addEventListener('change', function(){
    if(!this.value) return;

    fetch('get_jadwal_dokter.php?id_dokter='+this.value)
    .then(r=>r.text())
    .then(d=>jadwalDiv.innerHTML=d);

    fetch('get_hari_jadwal.php?id_dokter='+this.value)
    .then(r=>r.json())
    .then(h=>{ hariValid=h; });
});


tanggalInput.addEventListener('change', function(){
    const map={0:'Minggu',1:'Senin',2:'Selasa',3:'Rabu',
               4:'Kamis',5:'Jumat',6:'Sabtu'};
    let hari = map[new Date(this.value).getDay()];
    if(!hariValid.includes(hari)){
        alert('Dokter tidak praktik di hari tersebut');
        this.value='';
    }
});
</script>

</body>
</html>
