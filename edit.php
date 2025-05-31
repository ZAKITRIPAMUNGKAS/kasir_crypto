include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM transaksi WHERE id=$id");
    $data = $result->fetch_assoc();
}

if (isset($_POST['update'])) {
    $nama = $_POST['nama_crypto'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $tanggal = $_POST['tanggal'];

    $stmt = $conn->prepare("UPDATE transaksi SET nama_crypto=?, jumlah=?, harga=?, tanggal=? WHERE id=?");
    $stmt->bind_param("sddsi", $nama, $jumlah, $harga, $tanggal, $id);
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi</title>
</head>
<body>
    <h2>Edit Transaksi</h2>
    <form method="POST">
        <input type="text" name="nama_crypto" value="<?= $data['nama_crypto']; ?>" required>
        <input type="number" step="0.00000001" name="jumlah" value="<?= $data['jumlah']; ?>" required>
        <input type="number" step="0.01" name="harga" value="<?= $data['harga']; ?>" required>
        <input type="date" name="tanggal" value="<?= $data['tanggal']; ?>" required>
        <button type="submit" name="update">Update</button>
    </form>
</body>
</html>
