<?php
// Kết nối cơ sở dữ liệu
include '../includes/db.php'; // Đảm bảo đường dẫn đúng đến tệp kết nối cơ sở dữ liệu

// Kiểm tra xem dữ liệu có được gửi từ AJAX không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ yêu cầu AJAX
    $column = $_POST['column'];
    $row = $_POST['row'];
    $value = $_POST['value'];

    // Xác định tên cột cần cập nhật dựa trên chỉ số cột
    $columns = ['ma_nv', 'ho_ten']; // Thay đổi mảng này theo tên các cột của bạn
    $columnName = $columns[$column];

    // Kiểm tra chỉ số hợp lệ
    if (!isset($columnName)) {
        echo json_encode(['error' => 'Cột không hợp lệ']);
        exit;
    }

    // Chuẩn bị câu lệnh SQL để cập nhật dữ liệu
    $sql = "UPDATE dataTong SET $columnName = ? WHERE id = ?"; // Sử dụng id hoặc khóa chính của bảng
    $stmt = $conn->prepare($sql);
    
    // Lấy id dựa vào số dòng (bạn cần đảm bảo đúng cách để xác định id)
    $stmt->bind_param('si', $value, $row); // Gắn giá trị và id (cột kiểu string và id kiểu integer)
    
    // Thực thi câu lệnh SQL
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Dữ liệu đã được cập nhật']);
    } else {
        echo json_encode(['error' => 'Không thể cập nhật dữ liệu']);
    }
    
    // Đóng câu lệnh và kết nối
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Yêu cầu không hợp lệ']);
}
?>
