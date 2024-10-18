<?php
session_start();

// ユーザーがログインしていない場合
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo '<script>
        alert("ログインが必要です。");
        window.location.href = "login.php";
    </script>';
    exit();
}else if ($_SESSION['yakusyoku'] === 'バイト') {
    echo '<script>
        alert("権限がありません。");
            window.location.href = "login.php";
    </script>';
    include 'login_out.php';
    exit();
}
?>
