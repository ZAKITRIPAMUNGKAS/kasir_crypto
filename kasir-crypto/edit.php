<?php
include 'config/koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM transaksi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan.");
}

$cryptoList = [
    'Bitcoin' => 65000.00,
    'Ethereum' => 3800.00,
    'Solana' => 150.00,
    'BNB' => 600.00,
    'Cardano' => 0.45,
    'Ripple' => 0.50,
    'Dogecoin' => 0.15
];

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
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background: #f4f6f8;
    color: #333;
    line-height: 1.6;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.container {
    background: #ffffff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
}

h1 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 24px;
    color: #2c3e50;
}

form select,
form input[type="number"],
form input[type="date"] {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    transition: border 0.3s ease;
}

form select:focus,
form input:focus {
    border-color: #3498db;
    outline: none;
    background-color: #f0faff;
}

button[type="submit"] {
    width: 100%;
    background-color: #3498db;
    color: #fff;
    padding: 12px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #2980b9;
}

.back-btn {
    width: 100%;
    background-color: #95a5a6;
    color: #fff;
    padding: 12px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 10px;
    transition: background-color 0.3s ease;
    text-align: center;
    text-decoration: none;
    display: inline-block;
}

.back-btn:hover {
    background-color: #7f8c8d;
}

@media (max-width: 600px) {
    .container {
        padding: 20px;
    }

    h1 {
        font-size: 20px;
    }
}

</style>
</head>
<body>
<div class="container">
    <h1>Edit Transaksi</h1>
    <form method="POST">
        <select name="nama_crypto" required>
            <option value="">Pilih Nama Crypto</option>
            <?php foreach ($cryptoList as $crypto => $harga): ?>
                <option value="<?= $crypto ?>" <?= $crypto == $data['nama_crypto'] ? 'selected' : '' ?>><?= htmlspecialchars($crypto) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" step="0.00000001" name="jumlah" value="<?= $data['jumlah'] ?>" placeholder="Jumlah" required>
        <input type="number" step="0.01" name="harga" value="<?= $data['harga'] ?>" placeholder="Harga (Rp)" required>
        <input type="date" name="tanggal" value="<?= $data['tanggal'] ?>" required>
        <button type="submit" name="update">Update</button>
    </form>
    <a href="index.php" class="back-btn">Kembali</a>
</div>
</body>
</html>

