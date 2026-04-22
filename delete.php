<?php
session_start();
// 刪除操作僅允許管理員執行。
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// 僅處理從管理頁送出的 POST 刪除請求。
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    $host = "localhost";
    $dbname = "my_blog";
    $username_db = "root";
    $password_db = "";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
        // 執行刪除指令
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        die("ErrorMessage:" . $e->getMessage());
    }
}

header("Location: admin.php");
exit;