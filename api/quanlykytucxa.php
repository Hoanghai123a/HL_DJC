<?php
session_start();
// if (isset($_SESSION['username'])) {
//   // kiểm tra permission
// } else {
//   header("Location: ../index.php");
//   exit; // Thoát khỏi script
// }
include 'db.php'; // Kết nối với cơ sở dữ liệu

header("Content-Type: application/json");

// Lấy phương thức HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Xử lý các yêu cầu khác nhau
switch ($method) {
  case 'GET':
    // Phân trang
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $pageSize = isset($_GET['pageSize']) ? (int)$_GET['pageSize'] : 10;
    $offset = ($page - 1) * $pageSize;

    // Lấy giá trị điều kiện từ URL nếu có
    $hoTen = isset($_GET['hoTen']) ? $conn->real_escape_string($_GET['hoTen']) : null;
    $congTy = isset($_GET['congTy']) ? $conn->real_escape_string($_GET['congTy']) : null;

    // Xây dựng câu điều kiện WHERE
    $whereConditions = [];
    if ($hoTen !== null) {
      $whereConditions[] = "HoTen LIKE '%$hoTen%'";
    }
    if ($congTy !== null) {
      $whereConditions[] = "CongTy = '$congTy'";
    }

    // Kết hợp các điều kiện lại với nhau
    $whereClause = "";
    if (count($whereConditions) > 0) {
      $whereClause = "WHERE " . implode(" AND ", $whereConditions);
    }

    // Lấy tổng số bản ghi
    $totalQuery = "SELECT COUNT(*) as total FROM quanlykytucxa $whereClause";
    $totalResult = $conn->query($totalQuery);
    $totalRow = $totalResult->fetch_assoc();
    $totalRecords = $totalRow['total'];

    // Tính toán tổng số trang
    $totalPages = ceil($totalRecords / $pageSize);

    // Lấy dữ liệu với phân trang và điều kiện
    $query = "SELECT * FROM quanlykytucxa $whereClause LIMIT $offset, $pageSize";
    $result = $conn->query($query);
    $items = [];

    while ($row = $result->fetch_assoc()) {
      $items[] = $row;
    }

    // Tạo liên kết next và previous
    $nextLink = ($page < $totalPages) ? "?page=" . ($page + 1) . "&pageSize=" . $pageSize : null;
    $prevLink = ($page > 1) ? "?page=" . ($page - 1) . "&pageSize=" . $pageSize : null;

    // Kết quả trả về dạng JSON
    $response = [
      'page' => $page,
      'pageSize' => $pageSize,
      'totalRecords' => $totalRecords,
      'totalPages' => $totalPages,
      'next' => $nextLink,
      'previous' => $prevLink,
      'results' => $items
    ];

    echo json_encode($response);
    break;

  case 'POST':
    // Thêm bản ghi mới
    $data = json_decode(file_get_contents("php://input"), true);

    $hoTen = isset($data['hoTen']) && !empty($data['hoTen']) ? $conn->real_escape_string($data['hoTen']) : die("Họ tên không được để trống");
    $ngayVao = isset($data['ngayVao']) ? $conn->real_escape_string($data['ngayVao']) : null;
    $ngayRa = isset($data['ngayRa']) ? $conn->real_escape_string($data['ngayRa']) : null;
    $tinhTrang = isset($data['tinhTrang']) ? $conn->real_escape_string($data['tinhTrang']) : null;
    $sdt = isset($data['sdt']) ? $conn->real_escape_string($data['sdt']) : null;
    $cccd = isset($data['cccd']) ? $conn->real_escape_string($data['cccd']) : null;
    $gioiTinh = isset($data['gioiTinh']) ? $conn->real_escape_string($data['gioiTinh']) : null;
    $queQuan = isset($data['queQuan']) ? $conn->real_escape_string($data['queQuan']) : null;
    $nguoiTuyen = isset($data['nguoiTuyen']) ? $conn->real_escape_string($data['nguoiTuyen']) : null;
    $congTy = isset($data['congTy']) ? $conn->real_escape_string($data['congTy']) : null;
    $maNV = isset($data['maNV']) ? $conn->real_escape_string($data['maNV']) : null;
    $thanhTien = isset($data['thanhTien']) ? floatval($data['thanhTien']) : die("Thành tiền không được để trống");
    $ghiChu = isset($data['ghiChu']) ? $conn->real_escape_string($data['ghiChu']) : null;
    $daphatNLD = isset($data['daphatNLD']) ? $conn->real_escape_string($data['daphatNLD']) : null;

    $query = "INSERT INTO quanlykytucxa (HoTen, NgayVao, NgayRa, TinhTrang, SDT, CCCD, GioiTinh, QueQuan, NguoiTuyen, CongTy, MaNV, ThanhTien, GhiChu, DaphatNLD) 
                  VALUES ('$hoTen', '$ngayVao', '$ngayRa', '$tinhTrang', '$sdt', '$cccd', '$gioiTinh', '$queQuan', '$nguoiTuyen', '$congTy', '$maNV', '$thanhTien', '$ghiChu', '$daphatNLD')";

    if ($conn->query($query) === TRUE) {
      $last_id = $conn->insert_id; // Lấy ID của bản ghi vừa được tạo
      $selectQuery = "SELECT * FROM quanlykytucxa WHERE id = $last_id";
      $result = $conn->query($selectQuery);
      $createdItem = $result->fetch_assoc();
      echo json_encode(['message' => 'Thêm thành công', 'data' => $createdItem]);
    } else {
      echo json_encode(['message' => 'Lỗi: ' . $conn->error]);
    }
    break;

  case 'PATCH':
    // Cập nhật bản ghi
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    $updates = [];

    foreach ($data as $key => $value) {
      if ($key != 'id') {
        $updates[] = "$key = '$value'";
      }
    }

    $query = "UPDATE quanlykytucxa SET " . implode(", ", $updates) . " WHERE id = '$id'";

    if ($conn->query($query) === TRUE) {
      $selectQuery = "SELECT * FROM quanlykytucxa WHERE id = $id";
      $result = $conn->query($selectQuery);
      $updatedItem = $result->fetch_assoc();
      echo json_encode(['message' => 'Cập nhật thành công', 'data' => $updatedItem]);
    } else {
      echo json_encode(['message' => 'Lỗi: ' . $conn->error]);
    }
    break;

  case 'DELETE':
    // Xóa bản ghi
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'];

    $query = "DELETE FROM quanlykytucxa WHERE id = '$id'";

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
