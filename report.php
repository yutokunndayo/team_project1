<?php

require_once 'conn.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$form = [
    'emp_no' => '',
    'name' => '',
    'deployment' => '',
    'data' => '',
    'comment' => '',
];
$errors = [];
$successMessage = '';

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['emp_no'] = trim($_POST['emp_no'] ?? '');
    $form['name'] = trim($_POST['name'] ?? '');
    $form['deployment'] = trim($_POST['deployment'] ?? '');
    $form['data'] = trim($_POST['data'] ?? '');
    $form['comment'] = trim($_POST['comment'] ?? '');

    if ($form['emp_no'] === '' || !ctype_digit($form['emp_no'])) {
        $errors[] = '社員番号は数字で入力してください。';
    }

    if ($form['name'] === '') {
        $errors[] = '名前を入力してください。';
    }

    if ($form['deployment'] === '') {
        $errors[] = '部署を入力してください。';
    }

    if (!in_array($form['data'], ['安全', '安全じゃない'], true)) {
        $errors[] = '安否状況を選択してください。';
    }

    if ($form['comment'] === '') {
        $errors[] = 'コメントを入力してください。';
    }

    if (mb_strlen($form['comment']) > 2000) {
        $errors[] = 'コメントは2000文字以内で入力してください。';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare('INSERT INTO report (emp_no, name, deployment, comment, data) VALUES (?, ?, ?, ?, ?)');
        if ($stmt) {
            $empNo = (int) $form['emp_no'];
            $stmt->bind_param('issss', $empNo, $form['name'], $form['deployment'], $form['comment'], $form['data']);
            if ($stmt->execute()) {
                $_SESSION['flash_message'] = '安否報告を登録しました。';
                header('Location: login.php');
                exit;
            } else {
                $errors[] = '保存に失敗しました。時間をおいて再度お試しください。';
            }
            $stmt->close();
        } else {
            $errors[] = '保存処理の準備に失敗しました。';
        }
    }
}

$recentReports = [];
$result = $conn->query('SELECT emp_no, name, deployment, data, comment, created_at FROM report ORDER BY created_at DESC LIMIT 10');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentReports[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>安否報告</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h1 class="h4 mb-3">安否報告フォーム</h1>
                        <p class="text-muted mb-4">必要項目を入力して送信してください。</p>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php foreach ($errors as $error): ?>
                                    <div><?php echo h($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($successMessage !== ''): ?>
                            <div class="alert alert-success" role="alert"><?php echo h($successMessage); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="report.php" novalidate>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="emp_no" class="form-label">社員番号</label>
                                    <input type="text" class="form-control" id="emp_no" name="emp_no" value="<?php echo h($form['emp_no']); ?>" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="name" class="form-label">名前</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo h($form['name']); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="deployment" class="form-label">部署</label>
                                    <input type="text" class="form-control" id="deployment" name="deployment" value="<?php echo h($form['deployment']); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label d-block">安否状況</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="data" id="safe" value="安全" <?php echo $form['data'] === '安全' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="safe">安全</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="data" id="unsafe" value="安全じゃない" <?php echo $form['data'] === '安全じゃない' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="unsafe">安全じゃない</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="comment" class="form-label">コメント</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="4" maxlength="2000" required><?php echo h($form['comment']); ?></textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">送信</button>
                                <a href="index.php" class="btn btn-outline-secondary">ダッシュボードへ戻る</a>
                            </div>
                        </form>
                    </div>
                </div>

                
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>