<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Kasir Crypto</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Login Kasir</h1>
            <form class="login-form" method="POST" action="login_proses.php">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    🚫 Login gagal! Username atau password salah.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
