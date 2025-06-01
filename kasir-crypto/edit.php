<?php
include 'config/koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$id = intval($_GET['id']);

// Ambil data transaksi berdasarkan ID
$stmt = $conn->prepare("SELECT * FROM transaksi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan.");
}

// Proses update jika form disubmit
if (isset($_POST['update'])) {
    $nama = $_POST['nama_crypto'];
    $jumlah = floatval($_POST['jumlah']);
    $harga = floatval($_POST['harga']);
    $tanggal = $_POST['tanggal'];

    $stmt = $conn->prepare("UPDATE transaksi SET nama_crypto = ?, jumlah = ?, harga = ?, tanggal = ? WHERE id = ?");
    $stmt->bind_param("sddsi", $nama, $jumlah, $harga, $tanggal, $id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h1>Edit Transaksi</h1>
    <form method="POST">
        <input type="text" name="nama_crypto" value="<?= htmlspecialchars($data['nama_crypto']) ?>" placeholder="Nama Crypto" required>
        <input type="number" step="0.00000001" name="jumlah" value="<?= $data['jumlah'] ?>" placeholder="Jumlah" required>
        <input type="number" step="0.01" name="harga" value="<?= $data['harga'] ?>" placeholder="Harga (Rp)" required>
        <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required>
        <button type="submit" name="update">Update</button>
    </form>
</div>
</body>
</html>
