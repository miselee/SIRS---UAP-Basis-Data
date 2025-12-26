<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user'])) {
    exit("403 Akses ditolak");
}


if(isset($_POST['id_kunjungan'])) {
    $id = (int)$_POST['id_kunjungan'];
    $id_pasien = (int)$_POST['id_pasien'];
    $id_dokter = (int)$_POST['id_dokter'];
    $id_ruangan = !empty($_POST['id_ruangan']) ? (int)$_POST['id_ruangan'] : "NULL";
    $jenis_kunjungan = $_POST['jenis_kunjungan'];
    $tanggal_kunjungan = $_POST['tanggal_kunjungan'];
    $jam_mulai = !empty($_POST['jam_mulai']) ? "'".$_POST['jam_mulai']."'" : "NULL";
    $jam_selesai = !empty($_POST['jam_selesai']) ? "'".$_POST['jam_selesai']."'" : "NULL";
    $status = $_POST['status'];

    $query = "
        UPDATE kunjungan SET
        id_pasien=$id_pasien,
        id_dokter=$id_dokter,
        id_ruangan=$id_ruangan,
        jenis_kunjungan='$jenis_kunjungan',
        tanggal_kunjungan='$tanggal_kunjungan',
        jam_mulai=$jam_mulai,
        jam_selesai=$jam_selesai,
        status='$status'
        WHERE id_kunjungan=$id
    ";

    if(mysqli_query($conn, $query)) {

            if($jenis_kunjungan=='rawat_inap') {
            $cek = mysqli_query($conn, "SELECT * FROM rawat_inap WHERE id_kunjungan=$id");
            if(mysqli_num_rows($cek)==0 && $id_ruangan!="NULL") {
                mysqli_query($conn, "INSERT INTO rawat_inap (id_kunjungan,id_ruangan,tanggal_masuk,status) 
                VALUES ($id, $id_ruangan, CURDATE(), 'dirawat')");
                mysqli_query($conn, "UPDATE ruangan SET status='dipakai' WHERE id_ruangan=$id_ruangan");
            }
        }

        header("Location: index.php");
        exit;
    } else {
        echo "Gagal update: " . mysqli_error($conn);
    }
}


else if(isset($_POST['id_pasien'])) {
    $id_pasien = (int)$_POST['id_pasien'];
    $id_dokter = (int)$_POST['id_dokter'];
    $id_ruangan = !empty($_POST['id_ruangan']) ? (int)$_POST['id_ruangan'] : "NULL";
    $jenis_kunjungan = $_POST['jenis_kunjungan'];
    $tanggal_kunjungan = $_POST['tanggal'] ?? date('Y-m-d'); // default hari ini
    $jam_mulai = !empty($_POST['jam_mulai']) ? "'".$_POST['jam_mulai']."'" : "NULL";
    $jam_selesai = !empty($_POST['jam_selesai']) ? "'".$_POST['jam_selesai']."'" : "NULL";
    $status = 'menunggu'; // default status
    $created_by = $_SESSION['user']['user_id']; // id user yang menambah

    $query = "
        INSERT INTO kunjungan 
        (id_pasien, id_dokter, id_ruangan, jenis_kunjungan, tanggal_kunjungan, jam_mulai, jam_selesai, status, created_by)
        VALUES
        ($id_pasien, $id_dokter, $id_ruangan, '$jenis_kunjungan', '$tanggal_kunjungan', $jam_mulai, $jam_selesai, '$status', $created_by)
    ";

    if(mysqli_query($conn, $query)) {
        $id_kunjungan = mysqli_insert_id($conn);

        if($jenis_kunjungan=='rawat_inap' && $id_ruangan!="NULL") {
            mysqli_query($conn, "INSERT INTO rawat_inap (id_kunjungan,id_ruangan,tanggal_masuk,status) 
            VALUES ($id_kunjungan, $id_ruangan, CURDATE(), 'dirawat')");
            mysqli_query($conn, "UPDATE ruangan SET status='dipakai' WHERE id_ruangan=$id_ruangan");
        }

        header("Location: index.php");
        exit;
    } else {
        echo "Gagal tambah: " . mysqli_error($conn);
    }
}


if(isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    // Jika rawat inap, kembalikan status ruangan
    $ri = mysqli_query($conn, "SELECT id_ruangan FROM rawat_inap WHERE id_kunjungan=$id");
    if($r = mysqli_fetch_assoc($ri)){
        mysqli_query($conn, "UPDATE ruangan SET status='tersedia' WHERE id_ruangan=".$r['id_ruangan']);
        mysqli_query($conn, "DELETE FROM rawat_inap WHERE id_kunjungan=$id");
    }

    if(mysqli_query($conn, "DELETE FROM kunjungan WHERE id_kunjungan=$id")){
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal hapus: " . mysqli_error($conn);
    }
}
?>
