<?php
session_start();
$_SESSION = array();//セッションの中身をすべて削除
session_destroy();//セッションを破壊

header("Refresh: 0; URL=login.php");
?>
