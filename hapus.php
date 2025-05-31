<?php
include 'koneksi.php';

$id = $_GET['id'];
$conn->query("DELETE FROM transaksi WHERE id=$id");

header("Location: index.php");
?>
