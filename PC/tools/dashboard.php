<div class="flex-c g5 pd5 noflow full">
    <div class="h2">Danh sách tổng</div>
    <div class="db-table noflow">
        <div class="flex g5 bt5">
            <input type="text" id="filter-name" placeholder="Tìm theo Tên">
            <button id="search-btn">Tìm kiếm</button>
        </div>
        <table>
            <thead>
                <tr id="datatong-head"></tr>
            </thead>
        </table>
        <div class="canflow hide-scroll">
            <table>
                <tbody id="datatong-tble"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(async function () {
        syncColumnWidths();
        window.addEventListener('resize', syncColumnWidths);
        // Hàm để tải dữ liệu từ API với tham số lọc tên
        async function loadData(filterName = '') {
            try {
                const columnHeaders = {
                    "id": "STT",
                    "maNV": "Mã NV",
                    "HoTen": "Họ tên",
                    "CCCD": "CCCD",
                    "NgaySinh": "Ngày sinh",
                    "DiaChi": "Địa chỉ",
                    "SDT": "SĐT",
                    "NhaChinh": "Nhà chính",
                    "CongTy": "Công ty",
                    "NgayVao": "Ngày vào làm",
                    "NgayNghi": "Ngày nghỉ",
                    "NguoiTuyen": "Người tuyển",
                    "GhiChu": "Ghi chú",
                    "TenGoc": "Tên gốc",
                    "NganHang": "Ngân hàng",
                    "STK": "Số TK",
                    "ChuTK": "Chủ TK",
                    "GhiChuTK": "Quan hệ"
                };
                // Thực hiện gọi API và xử lý lỗi nếu có
                var data = await app.get(`/api/dataTong.php${filterName ? '?name=' + encodeURIComponent(filterName) : ''}`);

                if (!data || !data.results || data.results.length === 0) {
                    console.error("Không có dữ liệu nào trả về từ API hoặc dữ liệu không đúng định dạng.");
                    $("#datatong-tble").html("<tr><td colspan='100%'>Không có dữ liệu phù hợp.</td></tr>");
                    return;
                }

                console.log(data);

                var key = [];
                var khongdung = ["CCCD", "NgaySinh", "DiaChi"];
                Object.keys(data.results[0]).forEach(keys => {
                    if (!khongdung.includes(keys)) {
                        key.push(keys);
                    }
                });

                var view = '';
                var tview = ``;
                key.forEach(fkey => { // table header
                    if (columnHeaders[fkey]) {
                        tview += `<th>${columnHeaders[fkey]}</th>`;
                    }
                });
                tview += "<th></th>"
                $("#datatong-head").html(tview);

                data.results.forEach(function (item, idx) {
                    var tview = ``;
                    key.forEach(fkey => {
                        if (fkey == 'id') {
                            tview += `<td>${idx + 1}</td>`;
                            return;
                        }
                        tview += `<td>${item[fkey]}</td>`;
                    });
                    view += `<tr>${tview}
                    <td class="tools">
                        <button class="edit-btn" id="${item.id}">Sửa</button>
                    </td>
                </tr>`;
                });

                $("#datatong-tble").html(view);
                addEventListeners(data, key);
                syncColumnWidths();

            } catch (error) {
                console.error("Lỗi khi tải dữ liệu từ API:", error);
                $("#datatong-tble").html("<tr><td colspan='100%'>Đã xảy ra lỗi khi tải dữ liệu.</td></tr>");
            }
        }

        // Gọi hàm loadData để tải dữ liệu ban đầu
        await loadData();

        // Sự kiện lọc dữ liệu khi bấm tìm kiếm
        $("#search-btn").click(() => {
            var filterName = $("#filter-name").val();
            loadData(filterName);
        });

        // Hàm để thêm sự kiện vào nút "Sửa"
        function addEventListeners(data, key) {
            $(".edit-btn").click(async function () {
                var itemID = this.id;
                var idData = {};
                await data.results.forEach(dt => {
                    if (dt.id == itemID) idData = dt;
                });
                var list_rq = await app.get("/api/yeucauSua.php?id_action=" + idData.id);
                console.log(list_rq);
                var history = "";
                await list_rq.results.forEach((dt, idx) => {
                    history += `<tr><td>${idx + 1}</td><td>${dt.trangthai}</td><td>${dt.username}</td><td><textarea>${dt.noidungsua}</textarea></td><td>${dt.mota}</td><td>${dt.create_date}</td><td><button class="apply_change" id="${dt.id}">Chấp nhận</button></tr>`;
                });
                var view = `
                <div class="flex-c edit-form g10">
                    <table>
                        <tr><td>Mã nhân viên</td><td>${idData.maNV}</td></tr>
                        <tr><td>Họ và tên</td><td>${idData.HoTen}</td></tr>
                        <tr><td>Ngân hàng</td><td><input id="NganHang_vl" type="text" value="${idData.NganHang}"></td></tr>
                        <tr><td>Số tài khoản</td><td><input id="STK_vl" type="text" value="${idData.STK}"></td></tr>
                        <tr><td>Chủ tài khoản</td><td><input id="ChuTK_vl" type="text" value="${idData.ChuTK}"></td></tr>
                        <tr><td>Ghi chú (Nếu khác tên đi làm)</td><td><input id="GhiChuTK_vl" type="text" value="${idData.GhiChuTK}"></td></tr>
                    </table>
                    <div class="flex-c g5">
                        <div class="flex">Ghi chú / comment:</div>
                        <textarea id="comment_txt" placeholder="sửa gấp...."></textarea>
                    </div>
                    <div class="flex g5 icenter">
                        <button class="create-btn">Tạo yêu cầu sửa</button>
                        <div id="res-status"></div>
                    </div>
                </div>
                <div class="flex-c edit-form g10">
                    <div class="h2">Yêu cầu sửa cũ</div>
                    <table><th>#</th><th>Trạng thái</th><th>Người yêu cầu</th><th>Nội dung</th><th>Ghi chú</th><th>Ngày tạo</th><tbody>${history}</tbody></table>
                </div>
            `;
                app.popup("Sửa/bổ sung thông tin " + idData.HoTen, view);
                $(".apply_change").click(async function(){
                    var changeDT = {};
                    var itemID=this.id;
                    await list_rq.results.forEach(dt => {
                        if (dt.id == itemID) changeDT = dt;
                    });
                    console.log(changeDT);
                    changeDT2=JSON.parse(changeDT.noidungsua);
                    changeDT2.id=changeDT.id_action;
                    var update=await app.patch("/api/dataTong.php",changeDT2);
                    console.log(update);
                });
                $(".create-btn").click(async function () {
                    var newdata = {};
                    await key.forEach(fkey => {
                        if (fkey == 'id') return;
                        if ($("#" + fkey + "_vl").val()) newdata[fkey] = $("#" + fkey + "_vl").val();
                    });
                    try {
                        var res = await app.post(`/api/yeucauSua.php`, {
                            id_action: idData.id,
                            nguoiyeucau: app.getCookie("user_id"),
                            mota: $("#comment_txt").val(),
                            phanloai: "Sửa STK",
                            noidungsua: JSON.stringify(newdata),
                            noidungcu: JSON.stringify(idData)

                        });
                        console.log(res);
                        if (res) {
                            $("#res-status").html("<ok>Thành công, yêu cầu sửa đã được gửi đi</ok>");
                            app.closePopup();
                        }
                    } catch (e) {
                        $("#res-status").html("<ng>Lỗi, tìm Hải!</ng>");
                        console.log(e);
                    }
                });
            });
        }
    });
    function syncColumnWidths() {
        // Lấy tất cả các cột từ bảng thead (datatong-head) và tbody (datatong-tble)
        const headColumns = document.querySelectorAll('#datatong-head th');
        const bodyColumns = document.querySelectorAll('#datatong-tble tr:first-child td');

        if (headColumns.length !== bodyColumns.length) {
            console.error('Số lượng cột không khớp giữa thead và tbody');
            return;
        }

        // Lấy chiều rộng tối đa của từng cột và đồng bộ giữa 2 bảng
        headColumns.forEach((headColumn, index) => {
            const bodyColumn = bodyColumns[index];
            const maxWidth = Math.max(headColumn.offsetWidth, bodyColumn.offsetWidth);

            // Cập nhật chiều rộng cho cả hai cột
            headColumn.style.minWidth = maxWidth + 'px';
            bodyColumn.style.minWidth = maxWidth + 'px';
            headColumn.style.maxWidth = (maxWidth + 1) + 'px';
            bodyColumn.style.maxWidth = (maxWidth + 1) + 'px';
        });
    }
</script>