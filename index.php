<?php include 'koneksi.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Penjualan Crypto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Kasir Penjualan Crypto</h1>

<form method="POST" action="tambah.php">
    <select name="nama_crypto" id="nama_crypto" required onchange="setHarga()">
        <option value="" disabled selected>Pilih Crypto</option>
        <option value="Bitcoin">Bitcoin (BTC)</option>
        <option value="Ethereum">Ethereum (ETH)</option>
        <option value="Solana">Solana (SOL)</option>
        <option value="BNB">BNB</option>
        <option value="Cardano">Cardano (ADA)</option>
        <option value="Ripple">Ripple (XRP)</option>
        <option value="Dogecoin">Dogecoin (DOGE)</option>
    </select>

    <input type="number" step="0.00000001" name="jumlah" placeholder="Jumlah" required>
    <input type="number" step="0.01" name="harga" id="harga" placeholder="Harga (USD)" required readonly>
    <input type="date" name="tanggal" required>
    <button type="submit">Tambah Transaksi</button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>Nama Crypto</th>
        <th>Jumlah</th>
        <th>Harga</th>
        <th>Tanggal</th>
        <th>Total</th>
        <th>Aksi</th>
    </tr>

    <?php
    $result = $conn->query("SELECT * FROM transaksi");
    while ($row = $result->fetch_assoc()) {
        $total = $row['jumlah'] * $row['harga'];
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['nama_crypto']}</td>
            <td>{$row['jumlah']}</td>
            <td>\${$row['harga']}</td>
            <td>{$row['tanggal']}</td>
            <td>\$" . number_format($total, 2) . "</td>
            <td>
                <a href='edit.php?id={$row['id']}'>Edit</a> | 
                <a href='hapus.php?id={$row['id']}' onclick=\"return confirm('Hapus transaksi ini?')\">Hapus</a>
            </td>
        </tr>";
    }
    ?>
</table>

<script>
function setHarga() {
    const hargaInput = document.getElementById('harga');
    const cryptoSelect = document.getElementById('nama_crypto');
    const selectedCrypto = cryptoSelect.value;

    // Harga statis (contoh saja)
    const hargaCrypto = {
        'Bitcoin': 65000.00,
        'Ethereum': 3800.00,
        'Solana': 150.00,
        'BNB': 600.00,
        'Cardano': 0.45,
        'Ripple': 0.50,
        'Dogecoin': 0.15
    };

    hargaInput.value = hargaCrypto[selectedCrypto] || '';
}
</script>

</body>
</html>
