
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRYPTO TRANSACTION RECEIPT</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Roboto+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --neon-blue: #0ff0fc;
            --neon-pink: #ff2a6d;
            --neon-purple: #d300c5;
            --dark-bg: #0d0221;
            --darker-bg: #070119;
            --glow: 0 0 10px var(--neon-blue), 0 0 20px var(--neon-blue);
        }

        body {
            font-family: 'Roboto Mono', monospace;
            background-color: var(--dark-bg);
            color: #e2f1ff;
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            border: 1px solid var(--neon-blue);
            border-radius: 0;
            box-shadow: var(--glow);
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, 
                var(--neon-pink), 
                var(--neon-blue), 
                var(--neon-purple));
            animation: scanline 3s linear infinite;
        }

        @keyframes scanline {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        h2 {
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue);
            letter-spacing: 2px;
            font-weight: 700;
            position: relative;
        }

        h2::after {
            content: "";
            display: block;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, 
                transparent, 
                var(--neon-blue), 
                transparent);
            margin-top: 0.5rem;
        }

        .receipt-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
        }

        .receipt-item {
            display: contents;
        }

        .receipt-label {
            color: var(--neon-pink);
            text-align: right;
            padding-right: 1rem;
            font-weight: 300;
        }

        .receipt-value {
            font-weight: 500;
            text-shadow: 0 0 2px rgba(255,255,255,0.3);
        }

        .crypto-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            background: var(--darker-bg);
            color: var(--neon-blue);
            border: 1px solid var(--neon-blue);
            border-radius: 4px;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .total-display {
            border-top: 1px dashed var(--neon-blue);
            margin-top: 1.5rem;
            padding-top: 1rem;
            font-size: 1.3rem;
            text-align: center;
            font-family: 'Orbitron', sans-serif;
            color: var(--neon-blue);
            text-shadow: 0 0 5px var(--neon-blue);
            position: relative;
        }

        .total-display::before {
            content: "/////";
            position: absolute;
            top: -0.8rem;
            left: 0;
            right: 0;
            text-align: center;
            color: var(--neon-blue);
            opacity: 0.3;
            letter-spacing: 3px;
        }

        .btn-print {
            display: block;
            margin: 2rem auto 0;
            padding: 0.8rem 1.5rem;
            background: transparent;
            color: var(--neon-blue);
            border: 1px solid var(--neon-blue);
            border-radius: 0;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-print:hover {
            background: var(--neon-blue);
            color: var(--dark-bg);
            text-shadow: 0 0 5px var(--dark-bg);
            box-shadow: var(--glow);
        }

        .btn-print::before {
            content: ">";
            position: absolute;
            left: -20px;
            transition: all 0.3s ease;
        }

        .btn-print:hover::before {
            left: 10px;
        }

        .corner-decoration {
            position: absolute;
            width: 20px;
            height: 20px;
            border-color: var(--neon-blue);
            border-style: solid;
            border-width: 0;
        }

        .corner-tl {
            top: 0;
            left: 0;
            border-top-width: 3px;
            border-left-width: 3px;
        }

        .corner-tr {
            top: 0;
            right: 0;
            border-top-width: 3px;
            border-right-width: 3px;
        }

        .corner-bl {
            bottom: 0;
            left: 0;
            border-bottom-width: 3px;
            border-left-width: 3px;
        }

        .corner-br {
            bottom: 0;
            right: 0;
            border-bottom-width: 3px;
            border-right-width: 3px;
        }

        @media print {
            body {
                box-shadow: none;
                margin: 0;
                border: none;
                padding: 1rem;
                background-color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="corner-decoration corner-tl"></div>
<div class="corner-decoration corner-tr"></div>
<div class="corner-decoration corner-bl"></div>
<div class="corner-decoration corner-br"></div>

<h2>CRYPTO TRANSACTION RECEIPT</h2>

<div class="receipt-grid">
    <div class="receipt-item">
        <div class="receipt-label">TRANSACTION ID</div>
        <div class="receipt-value">#<?= str_pad($data['id'], 6, '0', STR_PAD_LEFT) ?></div>
    </div>
    <div class="receipt-item">
        <div class="receipt-label">DATE/TIME</div>
        <div class="receipt-value"><?= $data['tanggal'] ?> 23:59:59</div>
    </div>
    <div class="receipt-item">
        <div class="receipt-label">CLIENT</div>
        <div class="receipt-value"><?= strtoupper(htmlspecialchars($data['nama_pengguna'])) ?></div>
    </div>
    <div class="receipt-item">
        <div class="receipt-label">ASSET</div>
        <div class="receipt-value"><span class="crypto-badge"><?= $data['nama_crypto'] ?></span></div>
    </div>
    <div class="receipt-item">
        <div class="receipt-label">QUANTITY</div>
        <div class="receipt-value"><?= $data['jumlah'] ?></div>
    </div>
    <div class="receipt-item">
        <div class="receipt-label">PRICE/UNIT</div>
        <div class="receipt-value">$<?= number_format($data['harga'], 2) ?></div>
    </div>
</div>

<div class="total-display">
    TOTAL: $<?= number_format($total, 2) ?>
</div>

<button class="btn-print" onclick="window.print()">PRINT RECEIPT</button>

<script>
    // Add some terminal-like effects
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.receipt-value, h2, .total-display');
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.style.opacity = '1';
            }, 100 * index);
        });
    });
</script>

</body>
</html>