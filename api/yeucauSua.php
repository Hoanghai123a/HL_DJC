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
        $pageSize = isset($_GET['pageSize']) ? (int)$_GET['pageSize'] : 10;
        $offset = ($page - 1) * $pageSize;

        // Lấy giá trị điều kiện từ URL nếu có
        $nguoiyeucau = isset($_GET['nguoiyeucau']) ? intval($_GET['nguoiyeucau']) : null;
        $phanloai = isset($_GET['phanloai']) ? $conn->real_escape_string($_GET['phanloai']) : null;
        $trangthai = isset($_GET['trangthai']) ? $conn->real_escape_string($_GET['trangthai']) : null;
        $id_action = isset($_GET['id_action']) ? $conn->real_escape_string($_GET['id_action']) : null;

        // Xây dựng câu điều kiện WHERE
        $whereConditions = [];
        if ($nguoiyeucau !== null) {
            $whereConditions[] = "nguoiyeucau = '$nguoiyeucau'";
        }
        if ($phanloai !== null) {
            $whereConditions[] = "phanloai = '$phanloai'";
        }
        if ($trangthai !== null) {
            $whereConditions[] = "trangthai = '$trangthai'";
        }
        if ($id_action !== null) {
            $whereConditions[] = "id_action = '$id_action'";
        }

        // Kết hợp các điều kiện lại với nhau
        $whereClause = "";
        if (count($whereConditions) > 0) {
            $whereClause = "WHERE " . implode(" AND ", $whereConditions);
        }

        // Lấy tổng số bản ghi
        $totalQuery = "SELECT COUNT(*) as total FROM yeucausua $whereClause";
        $totalResult = $conn->query($totalQuery);
        $totalRow = $totalResult->fetch_assoc();
        $totalRecords = $totalRow['total'];
        
        // Tính toán tổng số trang
        $totalPages = ceil($totalRecords / $pageSize);

        // Lấy dữ liệu với phân trang và điều kiện, sử dụng LEFT JOIN để xử lý trường hợp nguoiyeucau là NULL
        $query = "
        SELECT yeucausua.*, users.username
        FROM yeucausua
        LEFT JOIN users ON yeucausua.nguoiyeucau = users.id
        $whereClause
        LIMIT $offset, $pageSize";
        $result = $conn->query($query);
        $items = [];

        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        // Tạo liên kết next và previous
        $nextLink = ($page < $totalPages) ? "?page=" . ($page + 1) . "&pageSize=" . $pageSize : null;
        $prevLink = ($page > 1) ? "?page=" . ($page - 1) . "&pageSize=" . $pageSize : null;

        // Kết quả trả về dạng panel
        $response = [
            'sql' => $totalQuery,
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
        // Thêm một yêu cầu sửa mới
        $data = json_decode(file_get_contents("php://input"), true);

        $nguoiyeucau = intval($data['nguoiyeucau']);
        $noidungsua = $data['noidungsua'];
        $trangthai = "Create";
        $mota = $data['mota'];
        $phanloai = $data['phanloai'];
        $phanhoi = "";
        $id_action = $data['id_action'];
        $noidungcu = $data['noidungcu'];

        $query = "INSERT INTO yeucausua (nguoiyeucau, phanloai,id_action, noidungcu, noidungsua, trangthai, create_date, mota, phanhoi) 
                  VALUES ('$nguoiyeucau', '$phanloai','$id_action', '$noidungcu', '$noidungsua', '$trangthai', NOW(), '$mota', '$phanhoi')";

        if ($conn->query($query) === TRUE) {
            $last_id = $conn->insert_id; // Lấy ID của bản ghi vừa được tạo
            $selectQuery = "SELECT * FROM yeucausua WHERE id = $last_id";
            $result = $conn->query($selectQuery);
            $createdItem = $result->fetch_assoc();
            echo json_encode(['message' => 'Thêm yêu cầu sửa thành công', 'data' => $createdItem]);
        } else {
            echo json_encode(['message' => 'Lỗi: ' . $conn->error]);
        }
        break;

    case 'PATCH':
        // Cập nhật thông tin yêu cầu sửa
        parse_str(file_get_contents("php://input"), $data);
        $id = $data['id'];
        $updates = [];

        foreach ($data as $key => $value) {
            if ($key != 'id') {
                $updates[] = "$key = '$value'";
            }
        }

        $query = "UPDATE yeucausua SET " . implode(", ", $updates) . ", update_date = NOW() WHERE id = '$id'";

        if ($conn->query($query) === TRUE) {
            $selectQuery = "SELECT * FROM yeucausua WHERE id = $id";
            $result = $conn->query($selectQuery);
            $updatedItem = $result->fetch_assoc();
            echo json_encode(['message' => 'Cập nhật thành công', 'data' => $updatedItem]);
        } else {
            echo json_encode(['message' => 'Lỗi: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        // Xóa yêu cầu sửa
        parse_str(file_get_contents("php://input"), $data);
        $id = $data['id'];

        $query = "DELETE FROM yeucausua WHERE id = '$id'";

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
