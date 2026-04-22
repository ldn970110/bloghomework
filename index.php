<?php
session_start();
// 依 session 判斷目前是否為已登入的管理員。
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
// 統一時間格式輸出使用台北時區。
date_default_timezone_set("Asia/Taipei");
$host = "localhost";
$dbname = "my_blog";
$username = "root";
$password = "";

try {
    // 建立 PDO 連線，並設定 UTF-8 避免中文亂碼。
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // 依建立時間由新到舊載入文章清單。
    $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ErrorMessage:" . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的部落格</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="main-header">
        <div class="header-wrapper">
            <a href="index.php" class="site-logo">我的部落格</a>
            <div class="header-nav">
                <!-- 已登入管理員顯示管理與登出按鈕，否則顯示登入按鈕 -->
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
        <h1>文章列表</h1>
        <?php if (empty($posts)): ?>
            <p>目前沒有文章。</p>
        <?php else: ?>
            <ul class="post-list">
                <?php foreach ($posts as $post): ?>
                    <li>
                        <a href="post.php?id=<?= $post['id'] ?>">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                        <span class="date"><?= date("Y-m-d H:i", strtotime($post['created_at'])) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>

</html>