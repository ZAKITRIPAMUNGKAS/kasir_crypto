<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $nama_crypto = $_POST['nama_crypto'];
        $stok = intval($_POST['stok']);
        $harga = floatval($_POST['harga']);
        $stmt = $conn->prepare("INSERT INTO gudang (nama_crypto, stok, harga) VALUES (?, ?, ?)");
        $stmt->bind_param("sif", $nama_crypto, $stok, $harga);
        $stmt->execute();
    } elseif (isset($_POST['delete']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM gudang WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif (isset($_POST['edit']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $nama_crypto = $_POST['nama_crypto'];
        $stok = intval($_POST['stok']);
        $harga = floatval($_POST['harga']);
        $stmt = $conn->prepare("UPDATE gudang SET nama_crypto = ?, stok = ?, harga = ? WHERE id = ?");
        $stmt->bind_param("sifi", $nama_crypto, $stok, $harga, $id);
        $stmt->execute();
    }
    header("Location: gudang.php");
    exit();
}

$gudang = $conn->query("SELECT * FROM gudang ORDER BY nama_crypto ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Gudang Crypto</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8fafc;
        color: #1e293b;
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 100%;
        margin: 2rem auto;
        background-color: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .card-title {
        font-size: 24px;
        margin-bottom: 1rem;
        color: #4f46e5;
    }
    form {
        margin-bottom: 2rem;
    }
    input[type="text"], input[type="number"] {
        width: calc(50% - 10px);
        padding: 10px;
        margin: 5px;
        font-size: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
    }
    button {
        background-color: #6366f1;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #4f46e5;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }
    th, td {
        padding: 10px;
        border: 1px solid #e2e8f0;
        text-align: left;
    }
    th {
        background-color: #f1f5f9;
    }
    .btn.btn-primary {
        display: inline-block;
        background-color: #10b981;
        color: #fff;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        margin-top: 1rem;
    }
    .btn.btn-primary:hover {
        background-color: #059669;
    }
  </style>
</head>
<body>
<header>
  <div class="logo"><div class="logo-icon">$</div><div class="logo-text">Kasir Crypto</div></div>
  <div class="user-menu">
    <span class="user-greeting">Halo, Admin</span>
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>
</header>

<div class="container">
  <div class="card">
    <h2 class="card-title">Stok Gudang Crypto</h2>
    <form method="POST">
      <input type="text" name="nama_crypto" placeholder="Nama Crypto" required>
      <input type="number" name="stok" placeholder="Jumlah" required>
      <input type="number" step="0.01" name="harga" placeholder="Harga" required>
      <button type="submit" name="add">Tambah</button>
    </form>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Crypto</th>
          <th>Jumlah</th>
          <th>Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $gudang->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['nama_crypto']) ?></td>
            <td><?= $row['stok'] ?></td>
            <td><?= number_format($row['harga'], 2) ?></td>
            <td>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button type="submit" name="delete">Hapus</button>
              </form>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="text" name="nama_crypto" value="<?= htmlspecialchars($row['nama_crypto']) ?>" required>
                <input type="number" name="stok" value="<?= $row['stok'] ?>" required>
                <input type="number" step="0.01" name="harga" value="<?= $row['harga'] ?>" required>
                <button type="submit" name="edit">Edit</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <div style="margin-top: 2rem;">
    <a href="index.php" class="btn btn-primary">Kembali</a>
  </div>
</div>

</body>
</html>

