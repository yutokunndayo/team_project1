<?php
    session_start();
    require_once "conn.php";
 
   
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = trim($_POST["emp_no"]);
    $email = trim($_POST["email"]);
 
   
    if (empty($id) || empty($email)) {
        $error = "社員番号とメールアドレスを入力してください。";
    }
    else {
       
        $sql = "SELECT id, name, email, password FROM register WHERE id = ? AND email = ? LIMIT 1";
 
       
        $stmt = mysqli_prepare($conn, $sql);
 
 
        if ($stmt) {
      mysqli_stmt_bind_param($stmt, "ss", $id, $email);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $user = mysqli_fetch_assoc($result);
      mysqli_stmt_close($stmt);
 
 
      if ($user) {
        $_SESSION["id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["email"] = $user["email"];
        header("Location: new_password.php");
        exit();
      }
 
        $error = "社員番号またはメールアドレスが正しくありません。";
  } else {
      $error = "データベースエラーが発生しました。";
  }
}
}
 
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>forgot password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4" style="width: 100%; max-width: 400px;">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">パスワードを忘れた場合</h2>
                <p class="text-center mb-4">社員番号とメールアドレスを入力してください。</p>
               
                <form method="POST" action="forgot_password.php" novalidate>
                    <div class="mb-3">
                        <label for="emp_no" class="form-label">社員番号</label>
                        <input type="text" class="form-control" id="emp_no" name="emp_no" placeholder="例: 001" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">メールアドレス</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="例: user@example.com" required>
                    </div>
                    <?php if (isset($error)) echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error) . '</div>'; ?>
                    <button type="submit" class="btn btn-primary w-100">Check</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>