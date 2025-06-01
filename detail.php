<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$transaksi_id = $_GET['id'];
$transaksi = $conn->query("SELECT * FROM transaksi WHERE id = $transaksi_id")->fetch_assoc();
$items = $conn->query("SELECT * FROM transaksi_item WHERE transaksi_id = $transaksi_id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <!-- Use the same head section as index.php -->
</head>
<body>

<!-- Use the same header as index.php -->

<div class="container">
  <div class="card">
    <h2 class="card-title">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      Detail Transaksi #<?= $transaksi['id'] ?>
    </h2>
    
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Nama Pembeli</label>
        <div class="form-control" style="background: var(--bg);"><?= htmlspecialchars($transaksi['nama_pengguna']) ?></div>
      </div>
      
      <div class="form-group">
        <label class="form-label">Tanggal Transaksi</label>
        <div class="form-control" style="background: var(--bg);"><?= $transaksi['tanggal'] ?></div>
      </div>
    </div>
    
    <div class="table-container" style="margin-top: 2rem;">
      <table>
        <thead>
          <tr>
            <th>Crypto</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($item = $items->fetch_assoc()): ?>
            <tr>
              <td><?= $item['nama_crypto'] ?></td>
              <td><?= $item['jumlah'] ?></td>
              <td>$<?= number_format($item['harga'], 2) ?></td>
              <td>$<?= number_format($item['jumlah'] * $item['harga'], 2) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align: right; font-weight: bold;">Total</td>
            <td style="font-weight: bold;">$<?= number_format($transaksi['total'], 2) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <div style="margin-top: 2rem;">
      <a href="index.php" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Kembali
      </a>
    </div>
  </div>
</div>

<!-- Use the same footer as index.php -->

</body>
</html>