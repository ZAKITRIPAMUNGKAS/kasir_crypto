<?php
$koneksi = new mysqli("localhost", "root", "", "db_kasir_crypto");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$data = null;
$total = 0.00;

if ($id > 0) {
    $stmt = $koneksi->prepare("SELECT * FROM transaksi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $total = $data['jumlah'] * $data['harga'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRYPTO RECEIPT</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-blue: #0ff0fc;
            --dark-bg: #0a0a12;
            --glow: 0 0 8px var(--neon-blue);
        }

        body {
            font-family: 'Roboto Mono', monospace;
            background-color: var(--dark-bg);
            color: #e2f1ff;
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            border: 1px solid var(--neon-blue);
            box-shadow: var(--glow);
        }

        h1 {
            text-align: center;
            color: var(--neon-blue);
            margin-bottom: 1.5rem;
            font-weight: 500;
            letter-spacing: 1px;
        }

        .receipt-info {
            margin-bottom: 1.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.6rem;
        }

        .label {
            color: var(--neon-blue);
            opacity: 0.8;
        }

        .value {
            font-weight: 500;
        }

        .crypto-name {
            color: var(--neon-blue);
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed var(--neon-blue);
            margin: 1.5rem 0;
            opacity: 0.5;
        }

        .total {
            text-align: right;
            font-size: 1.2rem;
            margin-top: 1rem;
            color: var(--neon-blue);
        }

        .print-btn,
        .back-btn {
            display: block;
            margin: 2rem auto 0;
            padding: 0.6rem 1.2rem;
            background: transparent;
            color: var(--neon-blue);
            border: 1px solid var(--neon-blue);
            cursor: pointer;
            transition: all 0.2s;
        }

        .print-btn:hover,
        .back-btn:hover {
            background: var(--neon-blue);
            color: var(--dark-bg);
        }

        @media print {
            .print-btn,
            .back-btn {
                display: none;
            }
            body {
                border: none;
                box-shadow: none;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

<h1>CRYPTO TRANSACTION</h1>

<div class="receipt-info">
    <div class="info-row">
        <span class="label">ID:</span>
        <span class="value">#<?= isset($data['id']) ? str_pad($data['id'], 6, '0', STR_PAD_LEFT) : '000000' ?></span>
    </div>
    <div class="info-row">
        <span class="label">DATE:</span>
        <span class="value"><?= isset($data['tanggal']) ? $data['tanggal'] : '-' ?></span>
    </div>
    <div class="info-row">
        <span class="label">CLIENT:</span>
        <span class="value"><?= isset($data['nama_pengguna']) ? htmlspecialchars($data['nama_pengguna']) : '-' ?></span>
    </div>
</div>

<div class="divider"></div>

<div class="info-row">
    <span class="label">ASSET:</span>
    <span class="value crypto-name"><?= isset($data['nama_crypto']) ? $data['nama_crypto'] : '-' ?></span>
</div>
<div class="info-row">
    <span class="label">AMOUNT:</span>
    <span class="value"><?= isset($data['jumlah']) ? $data['jumlah'] : '0' ?></span>
</div>
<div class="info-row">
    <span class="label">PRICE:</span>
    <span class="value">$<?= isset($data['harga']) ? number_format($data['harga'], 2) : '0.00' ?></span>
</div>

<div class="divider"></div>

<div class="total">
    TOTAL: $<?= number_format($total, 2) ?>
</div>

<button class="print-btn" onclick="window.print()">Print Receipt</button>
<button class="back-btn" onclick="window.location.href='index.php'">Back to List</button>

</body>
</html>

