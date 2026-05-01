<?php
    session_start();
    require_once "conn.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {                
        $password = trim($_POST["password"] ?? "");
        $password_confirm = trim($_POST["password_confirm"] ?? "");
        $email = $_SESSION["email"] ?? "";

        if (empty($email)) {
            $error = "セッションが無効です。再度ログインしてください。";
        } elseif (empty($password) || empty($password_confirm)) {
            $error = "新しいパスワードを入力してください。";
        } elseif ($password !== $password_confirm) {
            $error = "パスワードが一致しません。";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE register SET password = ? WHERE email = ? LIMIT 1";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt === false) {
                $error = "データベースエラーが発生しました。";
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $hash, $email);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) === 1) {
                    mysqli_stmt_close($stmt);
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "パスワードの更新に失敗しました。メールアドレスが正しくない可能性があります。";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>new password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4" style="width: 100%; max-width: 400px;">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">新しいパスワードを設定</h2>
                <p class="text-center mb-4">新しいパスワードを入力してください。</p>
                
                <form method="POST" action="new_password.php" novalidate>
                    <div class="mb-3">
                        <label for="password" class="form-label">新しいパスワード</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="例: password123" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">新しいパスワード（確認）</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="例: password123" required>
                    </div>
                    <?php if (isset($error)) echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error) . '</div>'; ?>
                    <button type="submit" class="btn btn-primary w-100">reset password</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>