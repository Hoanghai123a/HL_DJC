<?php
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    function is_mobile() {
        global $user_agent;
        return preg_match('/(android|iphone|ipad|ipod|windows phone|iemobile|mobile)/i', $user_agent);
    }

    if (is_mobile()) {
        // Chuyển hướng đến phiên bản di động của trang đăng nhập
        header('Location: /mobile/login.php');
    } else {
        // Chuyển hướng đến phiên bản PC của trang đăng nhập
        header('Location: /pc/index.php');
    }
    exit;
?>
