<?php
$version = 1.0;
session_start();
include 'includes/db.php'; // Kết nối cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra người dùng trong cơ sở dữ liệu
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Lưu thông tin người dùng vào session
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Chuyển hướng 
            header('Location: dashboard.php');
        } else {
            $error = "Mật khẩu không đúng.";
        }
    } else {
        $error = "Tên đăng nhập không tồn tại.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?php echo $version; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Đăng nhập - <?php echo $version; ?></h2>
    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <form method="post" action="login.php">
        <label for="username">Tên đăng nhập:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Mật khẩu:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
