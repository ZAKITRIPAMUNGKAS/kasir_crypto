<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Penjualan Crypto</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <h1>💰 Kasir Crypto</h1>
        <nav>
            <span>Halo, <?= $_SESSION['username']; ?></span>
            <a href="logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a>
        </nav>
    </header>
    <main>

