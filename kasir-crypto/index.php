<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_transaksi'])) {
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Insert main transaction for each crypto
        foreach ($_POST['crypto'] as $item) {
            $total = $item['jumlah'] * $item['harga'];
            
            $stmt = $conn->prepare("INSERT INTO transaksi (nama_pengguna, nama_crypto, jumlah, harga, tanggal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdds", $_POST['nama_pengguna'], $item['nama'], $item['jumlah'], $item['harga'], $_POST['tanggal']);
            $stmt->execute();
            
            $transaksi_id = $conn->insert_id;
            
            // Also insert into transaksi_item for detailed records
            $stmt_item = $conn->prepare("INSERT INTO transaksi_item (transaksi_id, nama_crypto, jumlah, harga) VALUES (?, ?, ?, ?)");
            $stmt_item->bind_param("isdd", $transaksi_id, $item['nama'], $item['jumlah'], $item['harga']);
            $stmt_item->execute();
        }
        
        // Commit transaction
        $conn->commit();
        header("Location: index.php?success=1");
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}

// Get all transactions
$transaksi = $conn->query("SELECT * FROM transaksi ORDER BY tanggal DESC, id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kasir Crypto</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #6366f1;
      --primary-dark: #4f46e5;
      --primary-light: #a5b4fc;
      --danger: #f43f5e;
      --danger-dark: #e11d48;
      --success: #10b981;
      --success-dark: #059669;
      --warning: #f59e0b;
      --bg: #f8fafc;
      --surface: #ffffff;
      --text: #1e293b;
      --text-light: #64748b;
      --muted: #94a3b8;
      --border: #e2e8f0;
      --radius: 0.5rem;
      --radius-lg: 0.75rem;
      --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      --transition: all 0.2s ease;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .container {
      flex: 1;
      padding: 2rem;
      max-width: 1400px;
      margin: 0 auto;
      width: 100%;
    }

    header {
      background: var(--surface);
      padding: 1.25rem 2rem;
      box-shadow: var(--shadow);
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 50;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .logo-icon {
      background: var(--primary);
      color: white;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
    }

    .logo-text {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--primary-dark);
    }

    .user-menu {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .user-greeting {
      color: var(--text-light);
      font-size: 0.9rem;
    }

    .logout-btn {
      background: var(--danger);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: var(--radius);
      text-decoration: none;
      font-weight: 500;
      font-size: 0.85rem;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .logout-btn:hover {
      background: var(--danger-dark);
    }

    .card {
      background: var(--surface);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .card-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 1.25rem;
      color: var(--primary-dark);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .card-title svg {
      width: 1.1rem;
      height: 1.1rem;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-label {
      display: block;
      font-size: 0.85rem;
      font-weight: 500;
      color: var(--text);
      margin-bottom: 0.5rem;
    }

    .form-control {
      width: 100%;
      padding: 0.7rem 1rem;
      font-size: 0.9rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--bg);
      transition: var(--transition);
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px var(--primary-light);
      outline: none;
      background: var(--surface);
    }

    .btn {
      padding: 0.7rem 1.25rem;
      font-size: 0.9rem;
      border-radius: var(--radius);
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      border: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
    }

    .btn-block {
      width: 100%;
    }

    /* Crypto items styles */
    .crypto-items {
      margin-top: 1.5rem;
    }
    
    .crypto-item {
      background: var(--bg);
      border-radius: var(--radius);
      padding: 1rem;
      margin-bottom: 1rem;
      border: 1px solid var(--border);
      position: relative;
    }
    
    .remove-item {
      position: absolute;
      top: -10px;
      right: -10px;
      background: var(--danger);
      color: white;
      border: none;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 14px;
    }
    
    .add-item-btn {
      background: var(--success);
      color: white;
      border: none;
      border-radius: var(--radius);
      padding: 0.6rem 1rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: 1rem;
      font-size: 0.85rem;
    }
    
    .total-display {
      font-weight: bold;
      margin-top: 1.5rem;
      text-align: right;
      font-size: 1.1rem;
    }
    
    /* Table styles */
    .table-container {
      overflow-x: auto;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      margin-bottom: 1.5rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--surface);
      font-size: 0.9rem;
    }

    thead {
      background: var(--primary);
      color: white;
    }

    th {
      padding: 0.9rem 1rem;
      text-align: left;
      font-weight: 600;
    }

    td {
      padding: 0.8rem 1rem;
      border-bottom: 1px solid var(--border);
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover td {
      background: rgba(99, 102, 241, 0.05);
    }

    .badge {
      display: inline-block;
      padding: 0.3rem 0.6rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .badge-primary {
      background: var(--primary-light);
      color: var(--primary-dark);
    }

    .action-btns {
      display: flex;
      gap: 0.5rem;
    }

    .action-btn {
      padding: 0.4rem;
      border-radius: var(--radius);
      transition: var(--transition);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-light);
    }

    .action-btn:hover {
      background: var(--bg);
    }

    .action-btn.edit {
      color: var(--warning);
    }

    .action-btn.delete {
      color: var(--danger);
    }

    .action-btn.nota {
      color: var(--success);
    }

    footer {
      background: var(--surface);
      padding: 1.25rem;
      text-align: center;
      font-size: 0.85rem;
      color: var(--text-light);
      box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
      .container {
        padding: 1rem;
      }
      
      header {
        padding: 1rem;
      }
      
      .card {
        padding: 1.25rem;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
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
        <!-- Crypto items will be added here dynamically -->
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
    </div>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> Kasir Crypto | Dibuat dengan ❤️
</footer>

<script>
// Crypto price data
const hargaCrypto = {
  'Bitcoin': 65000.00,
  'Ethereum': 3800.00,
  'Solana': 150.00,
  'BNB': 600.00,
  'Cardano': 0.45,
  'Ripple': 0.50,
  'Dogecoin': 0.15
};

// Add new crypto item
document.getElementById('addCryptoItem').addEventListener('click', function() {
  const cryptoItems = document.getElementById('cryptoItems');
  const itemCount = cryptoItems.querySelectorAll('.crypto-item').length;
  const itemId = 'crypto_' + Date.now();
  
  const itemHTML = `
    <div class="crypto-item" id="${itemId}">
      <button type="button" class="remove-item" onclick="document.getElementById('${itemId}').remove(); calculateTotal()">
        &times;
      </button>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Jenis Crypto</label>
          <select name="crypto[${itemId}][nama]" class="form-control crypto-select" required onchange="updateHarga(this)">
            <option value="" disabled selected>Pilih Crypto</option>
            ${Object.keys(hargaCrypto).map(crypto => 
              `<option value="${crypto}">${crypto}</option>`
            ).join('')}
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

// Update price when crypto is selected
function updateHarga(selectElement) {
  const cryptoName = selectElement.value;
  const hargaInput = selectElement.closest('.crypto-item').querySelector('.crypto-harga');
  hargaInput.value = hargaCrypto[cryptoName] || '';
  calculateTotal();
}

// Calculate total transaction amount
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

// Initialize with one crypto item
document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('addCryptoItem').click();
});
</script>

</body>
</html>