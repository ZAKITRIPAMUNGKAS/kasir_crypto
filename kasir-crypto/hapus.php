<?php
include 'config/koneksi.php';

$id = intval($_GET['id']); // amankan input

// Hapus dulu item terkait di transaksi_item
$conn->query("DELETE FROM transaksi_item WHERE transaksi_id = $id");

// Baru hapus data di transaksi
$conn->query("DELETE FROM transaksi WHERE id = $id");

header("Location: index.php");
exit();
?>
