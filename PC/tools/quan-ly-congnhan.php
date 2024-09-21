<div class="flex-c g5 pd5 noflow full">
  <div class="h2">Danh sách Ký túc xá</div>
  <div class="db-table noflow">
    <div class="flex g5 bt5">
      <input type="text" id="filter-name" placeholder="Tìm theo Tên">
      <button id="search-btn">Tìm kiếm</button>
      <div class="flex right">
        <button id="add-cn-btn">Thêm người</button>
      </div>
    </div>
    <table>
      <thead>
        <tr id="kytucxa-head"></tr>
      </thead>
    </table>
    <div class="canflow hide-scroll">
      <table>
        <tbody id="kytucxa-tble"></tbody>
      </table>
    </div>
  </div>
</div>

<script>
  $(document).ready(async function() {
    $("#add-cn-btn").click(async function() {
      var view = `
        <div class="flex-c edit-form g10">
          <table>
            <tr><td>Họ tên</td><td><input type="text" id="text_HoTen"></td></tr>
            <tr><td>Ngày vào</td><td><input type="date" id="text_NgayVao"></td></tr>
            <tr><td>Số điện thoại</td><td><input type="text" id="text_SDT"></td></tr>
            <tr><td>CCCD</td><td><input type="text" id="text_CCCD"></td></tr>
            <tr><td>Giới tính</td><td><select id="text_GioiTinh"><option>Nam</option><option>Nữ</option></select></td></tr>
            <tr><td>Quê quán</td><td><input type="text" id="text_QueQuan"></td></tr>
            <tr><td>Người tuyển</td><td><input type="text" id="text_NguoiTuyen"></td></tr>
            <tr><td>Công ty</td><td><input type="text" id="text_CongTy"></td></tr>
            <tr><td>Mã nhân viên</td><td><input type="text" id="text_MaNV"></td></tr>
            <tr><td>Ghi chú</td><td><input type="text" id="text_GhiChu"></td></tr>
            <tr><td>Đã phạt NLĐ</td><td><input type="text" id="text_DaphatNLD"></td></tr>
          </table>
          <div class="flex"><button class="add" id="add-nguoilaodong">+ Thêm</button></div>
        </div>
      `;
      app.popup("Thêm người vào Ký túc xá!", view);
      $("#add-nguoilaodong").click(async function() {
        var data = {
          "hoTen": $("#text_HoTen").val(),
          "ngayVao": $("#text_NgayVao").val(),
          "ngayRa": null,
          "tinhTrang": "Online",
          "sdt": $("#text_SDT").val(),
          "cccd": $("#text_CCCD").val(),
          "gioiTinh": $("#text_GioiTinh").val(),
          "queQuan": $("#text_QueQuan").val(),
          "nguoiTuyen": $("#text_NguoiTuyen").val(),
          "congTy": $("#text_CongTy").val(),
          "maNV": $("#text_MaNV").val(),
          "thanhTien": 0,
          "ghiChu": $("#text_GhiChu").val(),
          "daphatNLD": $("#text_DaphatNLD").val()
        }
        console.log(data);
        var create = app.post("/api/quanlykytucxa.php", data);
        loadDataKTX();
      })
    });
    // Hàm để tải dữ liệu từ API với tham số lọc tên
    async function loadDataKTX(filterName = '') {
      try {
        const columnHeaders = {
          "id": "STT",
          "HoTen": "Họ tên",
          "NgayVao": "Ngày vào",
          "NgayRa": "Ngày ra",
          "TinhTrang": "Tình trạng",
          "SDT": "SĐT",
          "CCCD": "CCCD",
          "GioiTinh": "Giới tính",
          "QueQuan": "Quê quán",
          "NguoiTuyen": "Người tuyển",
          "CongTy": "Công ty",
          "MaNV": "Mã NV",
          "ThanhTien": "Thành tiền",
          "GhiChu": "Ghi chú",
          "DaphatNLD": "Đã phát NLD"
        };

        // Thực hiện gọi API và xử lý lỗi nếu có
        var data = await app.get(`/api/quanlykytucxa.php${filterName ? '?name=' + encodeURIComponent(filterName) : ''}`);

        if (!data || !data.results || data.results.length === 0) {
          console.error("Không có dữ liệu nào trả về từ API hoặc dữ liệu không đúng định dạng.");
          $("#kytucxa-tble").html("<tr><td colspan='100%'>Không có dữ liệu phù hợp.</td></tr>");
          return;
        }

        var key = [];
        var khongdung = []; // Các cột không cần hiển thị
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
        $("#kytucxa-head").html(tview);

        data.results.forEach(function(item, idx) {
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

        $("#kytucxa-tble").html(view);
        addEventListeners(data, key);
        syncColumnWidths();
        window.addEventListener('resize', syncColumnWidths);

        $(".edit-btn").click(async function() {
          var itemID = this.id;
          var idData = {};
          await data.results.forEach(dt => {
            if (dt.id == itemID) idData = dt;
          });
          console.log(idData);
          var view = `
                <div class="flex-c edit-form g10">
                    <table>
                        <tr><td>Họ tên</td><td>${idData.HoTen}</td></tr>
                        <tr><td>Ngày vào</td><td><input id="NgayVao_vl" type="date" value="${idData.NgayVao}"></td></tr>
                        <tr><td>Ngày ra</td><td><input id="NgayRa_vl" type="date" value="${idData.NgayRa}"></td></tr>
                        <tr><td>Tình trạng</td><td><input id="TinhTrang_vl" type="text" value="${idData.TinhTrang}"></td></tr>
                        <tr><td>SĐT</td><td><input id="SDT_vl" type="text" value="${idData.SDT}"></td></tr>
                        <tr><td>Ghi chú</td><td><input id="GhiChu_vl" type="text" value="${idData.GhiChu}"></td></tr>
                    </table>
                    <div class="flex-c g5">
                        <div class="flex">Ghi chú / comment:</div>
                        <textarea id="comment_txt" placeholder="Ghi chú thêm..."></textarea>
                    </div>
                    <div class="flex g5 icenter">
                        <button class="create-btn">Sửa</button>
                        <div id="res-status"></div>
                    </div>
                </div>`;

          app.popup("Sửa thông tin " + idData.HoTen, view);

          $(".create-btn").click(async function() {
            var newdata = {
              id: itemID
            };
            if ($("#NgayRa_vl").val() != "") {
              newdata.TinhTrang = "Out";
            }
            await key.forEach(fkey => {
              if ($("#" + fkey + "_vl").val()) newdata[fkey] = $("#" + fkey + "_vl").val();
            });
            try {
              var res = await app.patch(`/api/quanlykytucxa.php`, newdata);
              console.log(res);
              if (res) {
                $("#res-status").html("<ok>Thành công, yêu cầu sửa đã được gửi đi</ok>");
                app.closePopup();
              }
            } catch (e) {
              $("#res-status").html("<ng>Lỗi, tìm admin!</ng>");
              console.log(e);
            }
          });
        });
      } catch (error) {
        console.error("Lỗi khi tải dữ liệu từ API:", error);
        $("#kytucxa-tble").html("<tr><td colspan='100%'>Đã xảy ra lỗi khi tải dữ liệu.</td></tr>");
      }
    }

    // Gọi hàm loadDataKTX để tải dữ liệu ban đầu
    await loadDataKTX();

    // Sự kiện lọc dữ liệu khi bấm tìm kiếm
    $("#search-btn").click(() => {
      var filterName = $("#filter-name").val();
      loadDataKTX(filterName);
    });

    // Hàm để thêm sự kiện vào nút "Sửa"
    function addEventListeners(data, key) {}
  });

  function syncColumnWidths() {
    const headColumns = document.querySelectorAll('#kytucxa-head th');
    const bodyColumns = document.querySelectorAll('#kytucxa-tble tr:first-child td');

    if (headColumns.length !== bodyColumns.length) {
      console.error('Số lượng cột không khớp giữa thead và tbody');
      return;
    }

    headColumns.forEach((headColumn, index) => {
      const bodyColumn = bodyColumns[index];
      const maxWidth = Math.max(headColumn.offsetWidth, bodyColumn.offsetWidth);

      headColumn.style.minWidth = maxWidth + 'px';
      bodyColumn.style.minWidth = maxWidth + 'px';
      headColumn.style.maxWidth = (maxWidth + 1) + 'px';
      bodyColumn.style.maxWidth = (maxWidth + 1) + 'px';
    });
  }
</script>