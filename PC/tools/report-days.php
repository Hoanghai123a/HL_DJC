<div class="flex-c g5 pd5">
    <div class="h2">Thêm công nhân</div>
    <div class="table-data fc g5">
        <table class="them-congnhan">
            <thead>
                <tr id="datatong-head"></tr>
            </thead>
            <tbody id="datatong-tble"></tbody>
        </table>
        <!-- Nút thêm dòng -->
        <div class="flex g5">
            <button id="addRow" class ="flex-c">Thêm dòng</button>
            <button id="saveAllData"class ="flex-c">Lưu toàn bộ dữ liệu</button>
        </div>
    </div>
</div>

<script>
$(document).ready(async function() {
    // Định nghĩa các tiêu đề cột, bao gồm cả STT
    const columnHeaders = {
        "id": "STT", // Số thứ tự
        "maNV": "Mã NV",
        "HoTen": "Họ tên",
        "CCCD": "CCCD",
        "NgaySinh": "Ngày sinh",
        "DiaChi": "Địa chỉ",
        "SDT": "SĐT",
        "NhaChinh": "Nhà chính",
        "CongTy": "Công ty",
        "NgayVao": "Ngày vào làm",
        "NguoiTuyen": "Người tuyển",
        "GhiChu": "GHI CHÚ",
        "TenGoc": "Tên gốc",
        "NganHang": "Ngân hàng",
        "STK": "Số tài khoản",
        "ChuTK": "Chủ tài khoản",
        "GhiChuTK": "Quan hệ (nếu khác tên đi làm)"
    };

    let currentId = 1; // Khởi tạo STT bắt đầu từ 1

    // Tạo tiêu đề bảng từ columnHeaders
    let tview = '';
    Object.values(columnHeaders).forEach(header => {
        tview += `<th>${header}</th>`;
    });
    $("#datatong-head").html(tview);

    // Hàm tạo dòng nhập liệu
    function createInputRow(id) {
        let view = `<tr>`;
        view += `<td>${id}</td>`; // Cột STT tự động
        Object.keys(columnHeaders).forEach(fkey => {
            if (fkey !== 'id') { // Bỏ qua trường STT vì nó tự động
                view += `<td><input type="text" id="${fkey}_${id}" name="${fkey}_${id}" placeholder="-" /></td>`;
            }
        });
        view += `</tr>`;
        return view;
    }

    // Thêm dòng nhập liệu đầu tiên
    $("#datatong-tble").html(createInputRow(currentId));

    // Sự kiện thêm dòng mới
    $("#addRow").click(function() {
        currentId++;
        $("#datatong-tble").append(createInputRow(currentId));
    });

    // Sự kiện lưu dữ liệu toàn bộ
    $("#saveAllData").click(function() {
        var newdata = []; // Khởi tạo mảng để chứa dữ liệu
        for (let i = 0; i <= currentId-1; i++) {
            let formData = { id: i };
            Object.keys(columnHeaders).forEach(fkey => {
                if (fkey !== 'id') { // Bỏ qua STT
                    formData[fkey] = $(`#${fkey}_${i}`).val(); // Lấy giá trị từ từng ô nhập
                }
            });
            
            newdata.push(formData); // Thêm formData vào mảng newdata

        // Gửi từng nhân viên mới tới API thông qua AJAX
        console.log(newdata); //
            newdata.forEach(function(data) {
                $.ajax({
                    url: '/api/dataTong.php', // Thay bằng đường dẫn thực tế tới API của bạn
                    type: 'POST',
                    data: data, // Gửi từng đối tượng formData (nhân viên mới)
                    success: function(response) {
                        console.log("Phản hồi từ server:", response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Có lỗi xảy ra khi thêm nhân viên:", xhr, status, error);
                    }
                });
            });
        }
    });

});

</script>