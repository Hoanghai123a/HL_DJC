<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hoàng Long DJC</title>
  <link rel="stylesheet" href="/lib/font/css/all.css">
  <link rel="stylesheet" href="src/app.css">
  <script src="src/jquery.js"></script>
  <script src="src/app.js"></script>
  <link rel="icon" type="image/png" href="/pc/assets/images/logo.png" />
</head>

<body>
  <div class="main-container">
    <div class="body-container" id="main-loader"></div>
    <div class="menu-container">
      <div class="items active" id="danhsach">
        <div class="item-logo"><i class="fa-solid fa-user-group"></i></div>
        <div class="item-name">Danh sách</div>
      </div>
      <div class="items" id="noiquy">
        <div class="item-logo"><i class="fa-solid fa-layer-group"></i></div>
        <div class="item-name">Nội quy</div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(async function() {
      $("#main-loader").load("danhsach.php");
      $(".items").click(async function() {
        $(".items").removeClass("active");
        $(this).addClass("active");
        $("#main-loader").load(this.id + ".php");
      })
    });
  </script>
</body>

</html>