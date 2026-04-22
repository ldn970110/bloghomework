<?php
session_start(); 
// 供前端判斷導覽列是否顯示管理功能。
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
date_default_timezone_set("Asia/Taipei");
$host = "localhost";
$dbname = "my_blog";
$username = "root";
$password = "";
try {
    // 建立資料庫連線並讀取單篇文章。
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("找不到這篇文章！ <a href='index.php'>回首頁</a>");
    }

} catch (PDOException $e) {
    die("ErrorMessage:" . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($post['title']) ?> - 我的部落格</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header class="main-header">
        <div class="header-wrapper">
            <a href="index.php" class="site-logo">我的部落格</a>
            <div class="header-nav">
                <!-- 已登入管理員顯示管理入口，訪客顯示登入入口 -->
                <?php if ($is_admin): ?>
                    <a href="admin.php" class="btn-login-top">管理面板</a>
                    <a href="logout.php" class="btn-login-top" style="background-color: #e74c3c; margin-left: 10px;">登出</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login-top">管理員登入</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

        <div class="container">
            <h1><?= htmlspecialchars($post['title']) ?></h1>
            <p class="date"><?= date("Y-m-d H:i", strtotime($post['created_at'])) ?></p>
            <div class="post-content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
            <p><a href="index.php">回文章列表</a></p>
        </div>
    </body>
</html>