<?php
session_start();
// 僅允許已登入管理員進入管理頁。
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
if (!$is_admin) {
    header("Location: login.php");
    exit;
}
date_default_timezone_set("Asia/Taipei");
$host = "localhost";
$dbname = "my_blog";
$username = "root";
$password = "";

try {
    // 連線資料庫並讀取文章列表。
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
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
<div class="container admin-container">
    <header class="main-header">
        <div class="header-wrapper">
            <a href="index.php" class="site-logo">我的部落格</a>
            <div class="header-nav">
                <a href="index.php" class="btn-login-top">回首頁</a>
                <a href="logout.php" class="btn-login-top" style="background-color: #e74c3c; margin-left: 10px;">登出</a>
            </div>
        </div>
    </header>
    <h1>管理介面</h1>
    <div class="admin-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">文章列表</h2>
            <a href="create.php" class="btn-login-top" style="background-color: #2ecc71;">+ 新增文章</a>
        </div>

        <?php if (empty($posts)): ?>
            <p>目前沒有文章。</p>
        <?php else: ?>
            <div class="admin-post-list">
                <?php foreach ($posts as $post): ?>
                    <!-- 單篇文章的管理區塊：查看、編輯、刪除 -->
                    <div class="admin-post-item">
                        <div class="admin-post-main">
                            <h3>
                                <a href="post.php?id=<?= $post['id'] ?>" style="text-decoration: none; color: #333;">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h3>
                            <p class="admin-post-preview date">
                                建立時間：<?= date("Y-m-d H:i", strtotime($post['created_at'])) ?>
                            </p>
                        </div>
                        <div class="admin-actions">
                            <a href="edit.php?id=<?= $post['id'] ?>" class="btn-login-top"
                                style="padding: 6px 12px; font-size: 0.9em;">編輯</a>

                            <form method="POST" action="delete.php" class="admin-delete-form"
                                onsubmit="return confirm('確定要刪除這篇文章嗎？');">
                                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                <button type="submit" class="btn-login-top admin-delete-btn"
                                    style="padding: 6px 12px; font-size: 0.9em;">刪除</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>

</html>