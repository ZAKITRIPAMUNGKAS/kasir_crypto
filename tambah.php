<?php
include 'koneksi.php';

$nama = $_POST['nama_crypto'];
$jumlah = $_POST['jumlah'];
$harga = $_POST['harga'];
$tanggal = $_POST['tanggal'];

$stmt = $conn->prepare("INSERT INTO transaksi (nama_crypto, jumlah, harga, tanggal) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdds", $nama, $jumlah, $harga, $tanggal);
$stmt->execute();

header("Location: index.php");
?>
