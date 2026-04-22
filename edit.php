<?php
session_start();
// 僅允許已登入管理員編輯文章。
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$dbname = "my_blog";
$username_db = "root";
$password_db = "";
$error = "";
$post = null;

try {
    // 建立資料庫連線。
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
    // 取得欲編輯文章 ID（由管理頁帶入）。
    $id = $_GET['id'] ?? null;
    if (!$id) {
        header("Location: admin.php");
        exit;
    }

    //處理表單更新請求 (POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (empty($title) || empty($content)) {
            $error = "標題與內容不能為空！";
        } else {
            // 使用預備語句更新資料，避免 SQL Injection。
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
            $stmt->execute([$title, $content, $id]);

            // 更新成功後跳回管理頁面
            header("Location: admin.php");
            exit;
        }
    }

    // 載入既有內容作為編輯表單預設值。
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header("Location: admin.php");
        exit;
    }

} catch (PDOException $e) {
    $error = "ErrorMessage:" . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編輯文章 - 我的部落格</title>
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
        <h2>編輯文章</h2>
        
        <?php if ($error): ?>
            <p class="error-message" style="color: #e74c3c;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>文章標題：</label><br>
                <input type="text" name="title" class="form-input" 
                       value="<?= htmlspecialchars($post['title']) ?>" required>
            </div>
            <div class="form-group">
                <label>文章內容：</label><br>
                <textarea name="content" class="form-textarea" required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-login-top" style="background-color: #3498db;">儲存修改</button>
                <a href="admin.php" class="btn-login-top" style="background-color: #95a5a6;">取消</a>
            </div>
        </form>
    </div>
</body>
</html>