<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chính - PC</title>
</head>
<body>
    <h2>Chào mừng, <?php echo $_SESSION['username']; ?>!</h2>
    <a href="logout.php">Đăng xuất</a>
</body>
</html>
