<?php
session_start();

// セッションの中身をすべて削除
$_SESSION = [];

// セッションIDも破棄（安全対策）
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// セッション完全破棄
session_destroy();

// ログインページへリダイレクト
header("Location: login.php");
exit;
