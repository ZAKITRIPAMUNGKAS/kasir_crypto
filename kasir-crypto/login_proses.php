<?php
session_start();
include 'config/koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = MD5(?)");
$query->bind_param("ss", $username, $password);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $_SESSION['login'] = true;
    $_SESSION['username'] = $username;
    header("Location: index.php");
} else {
    header("Location: login.php?error=1");
}
?>
