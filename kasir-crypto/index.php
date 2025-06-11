<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

// Fetch crypto list from database
$crypto_list = [];
$result = $conn->query("SELECT nama_crypto, harga FROM gudang");
while ($row = $result->fetch_assoc()) {
    $crypto_list[$row['nama_crypto']] = $row['harga'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_transaksi'])) {
    $conn->begin_transaction();
    
    try {
        foreach ($_POST['crypto'] as $item) {
            // Cek stok
            $stmt_stok = $conn->prepare("SELECT stok FROM gudang WHERE nama_crypto = ?");
            $stmt_stok->bind_param("s", $item['nama']);
            $stmt_stok->execute();
            $stmt_stok->bind_result($stok_tersedia);
            $stmt_stok->fetch();
            $stmt_stok->close();

            if ($stok_tersedia < $item['jumlah']) {
                throw new Exception("Stok {$item['nama']} tidak mencukupi. Tersedia: $stok_tersedia");
            }

            // Simpan ke transaksi
            $total = $item['jumlah'] * $item['harga'];
            $stmt = $conn->prepare("INSERT INTO transaksi (nama_pengguna, nama_crypto, jumlah, harga, tanggal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdds", $_POST['nama_pengguna'], $item['nama'], $item['jumlah'], $item['harga'], $_POST['tanggal']);
            $stmt->execute();
            $transaksi_id = $conn->insert_id;

            // Simpan ke transaksi_item
            $stmt_item = $conn->prepare("INSERT INTO transaksi_item (transaksi_id, nama_crypto, jumlah, harga) VALUES (?, ?, ?, ?)");
            $stmt_item->bind_param("isdd", $transaksi_id, $item['nama'], $item['jumlah'], $item['harga']);
            $stmt_item->execute();

            // Kurangi stok
            $stmt_update_stok = $conn->prepare("UPDATE gudang SET stok = stok - ? WHERE nama_crypto = ?");
            $stmt_update_stok->bind_param("ds", $item['jumlah'], $item['nama']);
            $stmt_update_stok->execute();
        }

        $conn->commit();
        header("Location: index.php?success=1");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}

$transaksi = $conn->query("SELECT * FROM transaksi ORDER BY tanggal DESC, id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kasir Crypto</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css" />
  <script>
    // Crypto price data from database
    const hargaCrypto = <?php echo json_encode($crypto_list); ?>;
  </script>
</head>
<body>

<header>
  <div class="logo">
    <div class="logo-icon">$</div>
    <div class="logo-text">Kasir Crypto</div>
  </div>
  <div class="user-menu">
    <span class="user-greeting">Halo, Admin</span>
    <a href="logout.php" class="logout-btn">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
        <polyline points="16 17 21 12 16 7"></polyline>
        <line x1="21" y1="12" x2="9" y2="12"></line>
      </svg>
      Logout
    </a>
  </div>
</header>

<div class="container">
  <div class="card">
    <h2 class="card-title">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      Tambah Transaksi Baru
    </h2>
    
    <form method="POST" id="transaksiForm">
      <div class="form-grid">
        <div class="form-group">
          <label for="nama_pengguna" class="form-label">Nama Pembeli</label>
          <input type="text" id="nama_pengguna" name="nama_pengguna" class="form-control" placeholder="Masukkan nama pembeli" required>
        </div>
        
        <div class="form-group">
          <label for="tanggal" class="form-label">Tanggal Transaksi</label>
          <input type="date" id="tanggal" name="tanggal" class="form-control" required>
        </div>
      </div>
      
      <div class="crypto-items" id="cryptoItems">
      </div>
      
      <button type="button" class="add-item-btn" id="addCryptoItem">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Tambah Crypto
      </button>
      
      <div class="total-display">
        Total Transaksi: <span id="totalTransaksi">$0.00</span>
        <input type="hidden" name="total_transaksi" id="totalTransaksiInput" value="0">
      </div>
      
      <button type="submit" name="submit_transaksi" class="btn btn-primary btn-block">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
          <polyline points="17 21 17 13 7 13 7 21"></polyline>
          <polyline points="7 3 7 8 15 8"></polyline>
        </svg>
        Simpan Transaksi
      </button>
    </form>
  </div>

  <div class="card">
    <h2 class="card-title">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="8" y1="6" x2="21" y2="6"></line>
        <line x1="8" y1="12" x2="21" y2="12"></line>
        <line x1="8" y1="18" x2="21" y2="18"></line>
        <line x1="3" y1="6" x2="3.01" y2="6"></line>
        <line x1="3" y1="12" x2="3.01" y2="12"></line>
        <line x1="3" y1="18" x2="3.01" y2="18"></line>
      </svg>
      Daftar Transaksi
    </h2>
    
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Pembeli</th>
            <th>Crypto</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Tanggal</th>
            <th>Total</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $transaksi->fetch_assoc()): 
            $total = $row['jumlah'] * $row['harga'];
          ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>
              <td><span class="badge badge-primary"><?= $row['nama_crypto'] ?></span></td>
              <td><?= $row['jumlah'] ?></td>
              <td>$<?= number_format($row['harga'], 2) ?></td>
              <td><?= $row['tanggal'] ?></td>
              <td>$<?= number_format($total, 2) ?></td>
              <td>
                <div class="action-btns">
                  <a href="edit.php?id=<?= $row['id'] ?>" class="action-btn edit" title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                  </a>
                  <a href="hapus.php?id=<?= $row['id'] ?>" class="action-btn delete" title="Hapus" onclick="return confirm('Hapus transaksi ini?')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                  </a>
                  <a href="nota.php?id=<?= $row['id'] ?>" class="action-btn nota" title="Cetak Nota">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="6 9 6 2 18 2 18 9"></polyline>
                      <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                      <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                  </a>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <a href="gudang.php" class="menu-link" style="display: block; margin: 1rem 0; padding: 0.5rem 1rem; border-radius: 0.5rem; background: var(--primary-light); color: var(--primary-dark); text-decoration: none; font-weight: 500;">Gudang</a>
    </div>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> Kasir Crypto
</footer>

<script>
document.getElementById('addCryptoItem').addEventListener('click', function() {
  const cryptoItems = document.getElementById('cryptoItems');
  const itemId = 'crypto_' + Date.now();
  
  let optionsHTML = '<option value="" disabled selected>Pilih Crypto</option>';
  for (const crypto in hargaCrypto) {
    optionsHTML += `<option value="${crypto}">${crypto}</option>`;
  }
  
  const itemHTML = `
    <div class="crypto-item" id="${itemId}">
      <button type="button" class="remove-item" onclick="document.getElementById('${itemId}').remove(); calculateTotal()">
        &times;
      </button>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Jenis Crypto</label>
          <select name="crypto[${itemId}][nama]" class="form-control crypto-select" required onchange="updateHarga(this)">
            ${optionsHTML}
          </select>
        </div>
        
        <div class="form-group">
          <label class="form-label">Jumlah</label>
          <input type="number" step="0.00000001" name="crypto[${itemId}][jumlah]" class="form-control crypto-jumlah" placeholder="0.00000000" required oninput="calculateTotal()">
        </div>
        
        <div class="form-group">
          <label class="form-label">Harga (USD)</label>
          <input type="number" step="0.01" name="crypto[${itemId}][harga]" class="form-control crypto-harga" placeholder="0.00" required readonly>
        </div>
      </div>
    </div>
  `;
  
  cryptoItems.insertAdjacentHTML('beforeend', itemHTML);
});

function updateHarga(selectElement) {
  const cryptoName = selectElement.value;
  const hargaInput = selectElement.closest('.crypto-item').querySelector('.crypto-harga');
  hargaInput.value = hargaCrypto[cryptoName] || '';
  calculateTotal();
}

function calculateTotal() {
  let total = 0;
  document.querySelectorAll('.crypto-item').forEach(item => {
    const jumlah = parseFloat(item.querySelector('.crypto-jumlah').value) || 0;
    const harga = parseFloat(item.querySelector('.crypto-harga').value) || 0;
    total += jumlah * harga;
  });
  
  document.getElementById('totalTransaksi').textContent = '$' + total.toFixed(2);
  document.getElementById('totalTransaksiInput').value = total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
  // Set default tanggal ke hari ini
  document.getElementById('tanggal').valueAsDate = new Date();
  // Tambahkan item crypto pertama
  document.getElementById('addCryptoItem').click();
});
</script>

</body>
</html>