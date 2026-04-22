<?php
session_start();
// 未登入管理員不可新增文章。
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得並清理使用者輸入。
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($title) || empty($content)) {
        $error = "標題與內容不能為空！";
    } else {
        $host = "localhost";
        $dbname = "my_blog";
        $username_db = "root";
        $password_db = "";

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
            // 使用預備語句寫入資料，避免 SQL Injection。
            $stmt = $pdo->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
            $stmt->execute([$title, $content]);
            
            header("Location: admin.php");
            exit;
        } catch (PDOException $e) {
            $error = "資料庫錯誤：" . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增文章 - 我的部落格</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-wrapper">
            <a href="index.php" class="site-logo">我的部落格</a>
            <div class="header-nav">
                <a href="admin.php" class="btn-login-top">回管理介面</a>
            </div>
        </div>
    </header>

    <div class="container" style="margin-top: 100px;">
        <h2>新增文章</h2>
        
        <?php if ($error): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>文章標題：</label><br>
                <input type="text" name="title" class="form-input" required>
            </div>
            <div class="form-group">
                <label>文章內容：</label><br>
                <textarea name="content" class="form-textarea" required></textarea>
            </div>
            <button type="submit" class="btn-login-top" style="background-color: #2ecc71;">發布文章</button>
        </form>
    </div>
</body>
</html>