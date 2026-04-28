<?php
session_start();
require_once "conn.php";

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}


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
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-header bg-primary text-white text-center py-4 border-0">
                        <div class="mb-2">
                            <i class="bi bi-shield-check fs-1"></i>
                        </div>
                        <h1 class="h4 mb-1 fw-bold">ABC 安全確認サイト</h1>
                        <p class="mb-0 small opacity-75">ログインしてください</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <?php if (isset($error) && $error !== ''): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo h($error); ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">メールアドレス</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="you@example.com" value="<?php echo h($email ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">パスワード</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="••••••••" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                                ログイン
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="email.php" class="link-secondary text-decoration-none small">Forget Password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>