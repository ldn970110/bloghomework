<?php
session_start();
// 登入頁：驗證帳密後建立管理員 session。
$host = "localhost";
$dbname = "my_blog";
$username_db = "root";
$password_db = "";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得使用者輸入。
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
        // 依帳號查詢使用者資料。
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$user]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && password_verify($pass, $userData['password'])) {
            // 登入成功後重生 session id，降低固定 Session 風險。
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = $userData['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "帳號或密碼錯誤！";
        }
    } catch (PDOException $e) {
        $error = "ErrorMessage:" . $e->getMessage();
    }
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
        </div>
    </header>
    
    <div class="container login-container">
        <?php if ($error): ?>
            <p class="error-message">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>帳號：</label><br>
                <input type="text" name="username" class="form-input" required>
            </div>
            <div class="form-group">
                <label>密碼：</label><br>
                <input type="password" name="password" class="form-input" required>
            </div>
            <button type="submit" class="btn-login-top">登入</button>
        </form>
        <p><a href="index.php">回首頁</a></p>
    </div>
</body>

</html>