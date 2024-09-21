<?php
    session_start();
    if(isset($_SESSION['username'])){
        // kiểm tra permission
    } else {
        header("Location: login.php");
        exit; // Quit the script
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoàng Long DJC</title>
    <!-- include libraries(jQuery, bootstrap) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <!-- Include XLSX-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="body-container">
        <div class="left-container">
            <div class="logo-container">
                <div class="logo"><img src="assets/images/logo.png"></div>
                <div class="name">HOÀNG LONG DJC</div>
            </div>
            <div class="fc g5" id="danhsach-tools"></div>
        </div>
        <div class="main-container">
            <div class="top-container">
                <div class="searchbox"><input type="text"></div>
                <div class="user">User</div>
            </div>
            <div id="main-load"></div>
        </div>
    </div>
    <script type="text/javascript" src="assets/js/app.js"></script>
</body>
</html>