<?php
include 'config/koneksi.php';

$nama = $_POST['nama_pengguna'];
$crypto = $_POST['nama_crypto'];
$jumlah = $_POST['jumlah'];
$harga = $_POST['harga'];
$tanggal = $_POST['tanggal'];

$stmt = $conn->prepare("INSERT INTO transaksi (nama_pengguna, nama_crypto, jumlah, harga, tanggal) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdds", $nama, $crypto, $jumlah, $harga, $tanggal);
$stmt->execute();

header("Location: index.php");
exit();
?>
