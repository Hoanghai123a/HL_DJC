<?php
$servername = "192.168.110.107:3306";
$username = "admin";
$password = "123";
$dbname = "hoanglongdjc";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
