<?php
session_start();
if(isset($_SESSION['username'])){
    // kiểm tra permission
} else {
    header("Location: ../index.php");
    exit; // Quit the script
}
include 'db.php'; // Kết nối với cơ sở dữ liệu

header("Content-Type: application/json");

// Lấy phương thức HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Xử lý các yêu cầu khác nhau
switch ($method) {
    case 'GET':
        // Thiết lập phân trang
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $pageSize = isset($_GET['pageSize']) ? (int)$_GET['pageSize'] : 50;
        $offset = ($page - 1) * $pageSize;
    
        // Lọc theo tên nếu có
        $nameFilter = isset($_GET['name']) ? $conn->real_escape_string($_GET['name']) : '';
    
        // Lấy tổng số bản ghi (có lọc theo tên)
        $totalQuery = "SELECT COUNT(*) as total FROM datatong";
        if ($nameFilter != '') {
            $totalQuery .= " WHERE HoTen LIKE '%$nameFilter%'"; // Thay 'name_column' bằng tên cột thực tế trong bảng của bạn
        }
        
        $totalResult = $conn->query($totalQuery);
        $totalRow = $totalResult->fetch_assoc();
        $totalRecords = $totalRow['total'];
    
        // Tính toán tổng số trang
        $totalPages = ceil($totalRecords / $pageSize);
    
        // Lấy dữ liệu với phân trang và lọc theo tên
        $query = "SELECT * FROM datatong";
        if ($nameFilter != '') {
            $query .= " WHERE HoTen LIKE '%$nameFilter%'"; // Thay 'name_column' bằng tên cột thực tế trong bảng của bạn
        }
        $query .= " LIMIT $offset, $pageSize";
        
        $result = $conn->query($query);
        $employees = [];
    
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    
        // Tạo liên kết next và previous
        $nextLink = ($page < $totalPages) ? "?page=" . ($page + 1) . "&pageSize=" . $pageSize . "&name=" . urlencode($nameFilter) : null;
        $prevLink = ($page > 1) ? "?page=" . ($page - 1) . "&pageSize=" . $pageSize . "&name=" . urlencode($nameFilter) : null;
    
        // Kết quả trả về dạng panel
        $response = [
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages,
            'next' => $nextLink,
            'previous' => $prevLink,
            'results' => $employees
        ];
    
        echo json_encode($response);
        break;
    
    case 'POST':
        // Thêm một nhân viên mới
        parse_str(file_get_contents("php://input"), $data);
        $maNV = $data['maNV'];
        $hoTen = $data['HoTen'];
        $cccd = $data['CCCD'];
        $ngaySinh = $data['NgaySinh'];
        $diaChi = $data['DiaChi'];
        $sdt = $data['SDT'];
        $nhaChinh = $data['NhaChinh'];
        $congTy = $data['CongTy'];
        $ngayVao = $data['NgayVao'];
        $ngayNghi = $data['NgayNghi'];
        $nguoiTuyen = $data['NguoiTuyen'];
        $ghiChu = $data['GhiChu'];
        $tenGoc = $data['TenGoc'];
        $nganHang = $data['NganHang'];
        $stk = $data['STK'];
        $chuTK = $data['ChuTK'];
        $ghiChuTK = $data['GhiChuTK'];
        
        $query = "INSERT INTO datatong (id,maNV,HoTen, CCCD, NgaySinh, DiaChi, SDT, NhaChinh, CongTy, NgayVao, NgayNghi, NguoiTuyen, GhiChu, TenGoc, NganHang, STK, ChuTK, GhiChuTK) 
                  VALUES (null,'$maNV','$hoTen', '$cccd', '$ngaySinh', '$diaChi', '$sdt', '$nhaChinh', '$congTy', '$ngayVao', '$ngayNghi', '$nguoiTuyen', '$ghiChu', '$tenGoc', '$nganHang', '$stk', '$chuTK', '$ghiChuTK')";

        if ($conn->query($query) === TRUE) {
            echo json_encode(['message' => 'Thêm nhân viên thành công']);
        } else {
            echo json_encode(['message' => 'Lỗi: '.$query. $conn->error]);
        }
        break;

    case 'PATCH':
        // Cập nhật thông tin nhân viên
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];
        $updates = [];

        foreach ($data as $key => $value) {
            if ($key != 'id') {
                $updates[] = "$key = '$value'";
            }
        }

        $query = "UPDATE datatong SET " . implode(", ", $updates) . " WHERE id = '$id'";

        if ($conn->query($query) === TRUE) {
            echo json_encode(['message' => 'Cập nhật thành công']);
        } else {
            echo json_encode(['message' => 'Lỗi: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        // Xóa nhân viên
        parse_str(file_get_contents("php://input"), $data);
        $id = $data['maNV'];
        if ($id=="ALL"){
            $query = "DELETE FROM datatong";
        } else {
            $query = "DELETE FROM datatong WHERE maNV = '$id'";
        }
        if ($conn->query($query) === TRUE) {
            echo json_encode(['message' => 'Xóa thành công']);
        } else {
            echo json_encode(['message' => 'Lỗi: ' . $conn->error]);
        }
        break;

    default:
        echo json_encode(['message' => 'Phương thức không được hỗ trợ']);
        break;
}

// Đóng kết nối
$conn->close();
?>
