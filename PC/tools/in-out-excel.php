<div class="container">
    <h2>Nhập Xuất File Excel</h2>
    <input type="file" id="upload-file" name="file" accept=".xlsx" />
    <button type="submit" id="upload-csv">Tải Lên và Nhập Dữ Liệu</button>
    <button type="submit" id="extract-csv">Xuất Dữ Liệu Ra File Excel</button>
    <div id="excel-data"></div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#upload-csv").click(function () {
            var files = $("#upload-file").prop("files");
            if (files.length == 0) {
                alert("Please select file to upload!");
                return false;
            } else {
                var reader = new FileReader(); // Tạo đối tượng FileReader để đọc file
                reader.onload = async function (event) {
                    var data = new Uint8Array(event.target.result); // Chuyển đổi dữ liệu file sang Uint8Array
                    var workbook = await XLSX.read(data, { type: 'array' }); // Đọc file Excel
                    var firstSheetName = workbook.SheetNames[0]; // Lấy tên của sheet đầu tiên
                    var worksheet = workbook.Sheets[firstSheetName]; // Lấy dữ liệu của sheet đầu tiên
                    // Chuyển đổi dữ liệu sheet thành JSON
                    var jsonData = await XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: "" });
                    console.log(jsonData);
                    var totalData = [];
                    await jsonData.forEach((key, value) => {
                        if (value != 0 && key[1] != "") {
                            totalData.push({
                                maNV: key[0],
                                HoTen: key[1],
                                CCCD: key[2],
                                NgaySinh: key[3],
                                DiaChi: key[4],
                                SDT: key[5],
                                NhaChinh: key[6],
                                CongTy: key[7],
                                NgayVao: key[8],
                                NgayNghi: key[9],
                                NguoiTuyen: key[10],
                                GhiChu: key[11],
                                TenGoc: key[12],
                                NganHang: key[13],
                                STK: key[14],
                                ChuTK: key[15],
                                GhiChuTK: key[16]
                            });
                        }
                    });
                    console.log(totalData);
                    if (totalData.length > 0) {
                        // Xóa dữ liệu cũ
                        await $.ajax({
                            url: "/api/dataTong.php",
                            type: "DELETE",
                            data: {
                                maNV:"ALL"
                            },
                            success: function (response) {
                                console.log('Xóa dữ liệu thành công:', response);
                            },
                            error: function () {
                                console.error('Lỗi xóa dữ liệu:', response);
                            }
                        });
                        // Thêm dữ liệu mới
                        await totalData.forEach(async function (item) {
                            await $.ajax({
                                url: "/api/dataTong.php",
                                type: "POST",
                                data: item,
                                success: function (response) {
                                    console.log('Thêm nhân viên thành công:', response);
                                },
                                error: function () {
                                    console.error('Lỗi thêm nhân viên:', response);
                                }
                            });
                        });
                    }
                };
                reader.readAsArrayBuffer(files[0]); // Đọc file dưới dạng ArrayBuffer
            }
        });
    });
    $(document).ready(function () {
    $("#extract-csv").click(function () {
        $.ajax({
            url: "/api/dataTong.php>?pageSize=5000", // Đường dẫn API để lấy dữ liệu
            type: "GET",
            success: function (response) {
                try {
                    var data = response; // Chuyển đổi chuỗi JSON sang đối tượng JavaScript
                    var jsonData = data.results; // Truy cập mảng dữ liệu bên trong phản hồi JSON
                    console.log(data);
                    exportObjectsToExcel(jsonData,"Data tổng.xlsx")
                } catch (e) {
                    console.error('Lỗi khi xử lý dữ liệu phản hồi:', e);
                    alert('Lỗi khi xử lý dữ liệu phản hồi từ server.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Lỗi lấy dữ liệu:', textStatus, errorThrown);
                alert('Lỗi khi lấy dữ liệu từ server. Vui lòng thử lại sau.');
            }
        });
    });
});
function exportObjectsToExcel(data, fileName = 'data.xlsx') {
    // Tạo một workbook mới
    const workbook = XLSX.utils.book_new();

    // Chuyển đổi mảng đối tượng thành một worksheet
    const worksheet = XLSX.utils.json_to_sheet(data);

    // Thêm worksheet vào workbook
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');

    // Xuất workbook ra file Excel
    XLSX.writeFile(workbook, fileName);
}
</script>