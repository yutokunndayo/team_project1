<?php 
    session_start();
    require_once "conn.php";

    // ログインボタンを押したとき
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = trim($_POST["emp_no"]);
    $name = trim($_POST["name"]);

    // 入力チェック
    if (empty($id) || empty($name)) {
        $error = "社員番号と名前を入力してください。";
    } 
    else {
        // ユーザー検索
        $sql = "SELECT id, name, email, password FROM register WHERE id = ? AND name = ? LIMIT 1";

        // プリペアドステートメント
        $stmt = mysqli_prepare($conn, $sql);


        if ($stmt) {
      mysqli_stmt_bind_param($stmt, "ss", $id, $name);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $user = mysqli_fetch_assoc($result);
      mysqli_stmt_close($stmt);
 
 
      if ($user) {
        $_SESSION["id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        header("Location: report.php");
        exit();
      }
 
        $error = "社員番号または名前が正しくありません。";
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
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class = "login-container">
        <h2>パスワードを忘れた場合</h2>
        <p>社員番号と名前を入力してください。</p>
    
    <form method="POST" action="forgot_password.php" novalidate>
            <div class="form-group">
                <label for="emp_no" class="form-label">社員番号</label>
                <input type="text" class="form-control" id="emp_no" name="emp_no" placeholder="例: 001" required>
            </div>
            <div class="form-group">
                <label for="name" class="form-label">名前</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="例: 山田太郎" required>
            </div>
        <?php if (isset($error)) echo $error; ?>
        <button type="submit" class="login-btn">Check</button>
    </form>
    </div>
</body>
</html>