<?php
session_start();
// 清除目前登入狀態並返回首頁。
session_destroy(); // 銷毀伺服器上的 Session 資料
header("Location: index.php"); // 踢回首頁
exit;