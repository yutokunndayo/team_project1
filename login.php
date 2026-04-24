<?php
session_start();
require_once "conn.php";


// ログインボタンを押したとき
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // 入力チェック
    if (empty($email) || empty($password)) {
        $error = "メールアドレスとパスワードを入力してください。";
    } else {
        // ユーザー検索
        $sql = "SELECT id, name, email, is_admin, password FROM register WHERE email = ? LIMIT 1";

        // プリペアドステートメント
        $stmt = mysqli_prepare($conn, $sql);



        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            $isValidPassword = false;
            if ($user) {

                $storedPassword = (string) $user["password"];
                $isValidPassword = ($password === $storedPassword) || password_verify($password, $storedPassword);
            }

            if ($isValidPassword) {
                $_SESSION["id"] = $user["id"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["is_admin"] = (int) ($user["is_admin"] ?? 0); // 管理者フラグをセッションに保存

                if ($_SESSION["is_admin"] === 1) {
                    header("Location: report.php");
                } else {
                    header("Location: report.php");
                }
                exit();
            }

            $error = "メールアドレスまたはパスワードが正しくありません。";
        } else {
            $error = "ログイン処理に失敗しました。もう一度お試しください。";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="login-container">
        <h2>こんにちは!</h2>
        <p>ABC 安全確認サイトへ...</p>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" placeholder="あなたのメールアドレス" required>
            </div>
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="login-btn">ログイン</button>
        </form>
        <?php if (isset($error)) echo $error; ?>
        <div><a href="forgot_password.php">Forgot Password</a></div>

    </div>
    </div>
</body>

</html>