<div class="danhsach">
  <div class="white-box">
    <div class="options">
      <div class="opt-items active">Tất cả</div>
      <div class="opt-items">Phòng nam</div>
      <div class="opt-items">Phòng nữ</div>
    </div>
  </div>
  <div class="dashboard">
    <div class="items">
      <name>Giường trống</name>
      <value>00</value>
    </div>
    <div class="items">
      <name>Đang sử dụng</name>
      <value id="usingCount">00</value>
    </div>
  </div>
  <div class="list-items" id="list-cnv">
    <div class="items">
      <div class="item-name">
        <div class="name">Diệp Văn Hùng</div>
        <div class="money">50000 đ</div>
      </div>
      <div class="item-details">
        <div class="days">
          10 ngày
        </div>
        <div class="records">
          <div class="item-in">
            <name>Ngày vào</name>
            <value>11/09/2024</value>
          </div>
          <div class="item-in">
            <name>Ngày ra</name>
            <value>21/09/2024</value>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    loadNhanvien();
    $(".opt-items").click(function() {
      $(".opt-items").removeClass("active");
      $(this).addClass("active");
    });
  });
  async function loadNhanvien() {
    var list = await app.get("/api/quanlykytucxa.php?pageSize=1000");
    if (list?.results && list.results.length > 0) {
      var view = ``,
        usingCount = 0;
      await list.results.forEach(cnhan => {
        if (cnhan.TinhTrang == "Online") {
          usingCount++;
          view += `<div class="items">
          <div class="item-name">
            <div class="name">${cnhan.HoTen}</div>
            <div class="money">50000 đ</div>
          </div>
          <div class="item-details">
            <div class="days">
              ${Math.floor((new Date()-new Date(cnhan.NgayVao))/(1000*60*60*24))} ngày
            </div>
            <div class="records">
              <div class="item-in">
                <name>Ngày vào</name>
                <value>${cnhan.NgayVao}</value>
              </div>
              <div class="item-in">
                <name>Phòng</name>
                <value>...</value>
              </div>
            </div>
          </div>
        </div>`;
        }
      });
      $("#list-cnv").html(view);
      $("#usingCount").html(usingCount);
    }
  }
</script>