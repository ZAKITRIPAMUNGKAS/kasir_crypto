<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Kasir Crypto</title>
    <style>
/* assets/style.css */

/* Root Variables */
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
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: var(--bg);
    color: var(--text);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    padding: 20px;
}

.login-box {
    background-color: var(--surface);
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    width: 100%;
    max-width: 400px;
    text-align: center;
    border: 1px solid var(--border);
}

.login-box h1 {
    margin-bottom: 24px;
    font-size: 28px;
    color: var(--primary-dark);
}

.login-form input {
    width: 100%;
    padding: 14px 16px;
    margin: 10px 0;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--bg);
    color: var(--text);
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.login-form input:focus {
    border-color: var(--primary);
    outline: none;
    background: #fff;
}

.login-form button {
    width: 100%;
    padding: 14px;
    margin-top: 20px;
    border: none;
    border-radius: 10px;
    background: var(--primary);
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

.login-form button:hover {
    background: var(--primary-dark);
    transform: scale(1.02);
}

.error-message {
    margin-top: 20px;
    padding: 12px;
    background-color: var(--danger);
    border-left: 6px solid var(--danger-dark);
    border-radius: 8px;
    color: #fff;
    font-weight: 500;
    text-align: left;
}

    </style>
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
