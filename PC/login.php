<?php
$version = 1.0;
session_start();
if(isset($_SESSION['username'])){
    // Chuyển hướng 
    header('Location: index.php');
}
include 'includes/db.php'; // Kết nối cơ sở dữ liệu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Escaping the user input to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);

    // Kiểm tra người dùng trong cơ sở dữ liệu
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Kiểm tra mật khẩu
        if ($password==$user['password']) {
            // Lưu thông tin người dùng vào session
            setcookie("user_id",$user['id'], time() + 360000, "/");
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            echo "Sang dashboard";
            // Chuyển hướng 
            header('Location: index.php');
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
