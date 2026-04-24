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

$loggedInUser = null;
$loggedInUserId = filter_var($_SESSION['id'] ?? '', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

$loggedInEmail = $_SESSION['email'] ?? '';

if ($loggedInUserId !== false) {
    $userStmt = $conn->prepare('SELECT id, `name`, `deployment`, email FROM register WHERE id = ? LIMIT 1');
    if ($userStmt) {
        $userStmt->bind_param('i', $loggedInUserId);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $loggedInUser = $userResult ? $userResult->fetch_assoc() : null;
        $userStmt->close();
    }
}

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if ($loggedInUser) {
    $form['emp_no'] = (string) $loggedInUser['id'];
    $form['name'] = (string) $loggedInUser['name'];
    $form['deployment'] = (string) $loggedInUser['deployment'];
} else {
    $errors[] = 'ログイン情報が見つかりません。もう一度ログインしてください。';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['data'] = trim($_POST['data'] ?? '');
    $form['comment'] = trim($_POST['comment'] ?? '');

    if (!$loggedInUser) {
        $errors[] = 'ログイン情報が見つかりません。もう一度ログインしてください。';
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
            try {
                $empNo = (int) $loggedInUser['id'];
                $name = (string) $loggedInUser['name'];
                $deployment = (string) $loggedInUser['deployment'];
                $stmt->bind_param('issss', $empNo, $name, $deployment, $form['comment'], $form['data']);
                if ($stmt->execute()) {
                    $_SESSION['flash_message'] = '安否報告を登録しました。';
                    header('Location: login.php');
                    exit;
                }

                $errors[] = '保存に失敗しました。時間をおいて再度お試しください。';
            } catch (mysqli_sql_exception $exception) {
                if ((int) $exception->getCode() === 1452) {
                    $errors[] = '登録されている社員IDを入力してください。';
                } else {
                    $errors[] = '保存に失敗しました。時間をおいて再度お試しください。';
                }
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700;800&display=swap" rel="stylesheet">
    <title>安否報告</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/app.css">
</head>

<body>
    <div class="dashboard-bg"></div>

    <div class="container-fluid">
        <div class="row min-vh-100">
            <aside class="col-12 col-lg-3 col-xl-2 sidebar-panel p-4 p-lg-3 p-xl-4">
                <div class="brand-box mb-4">
                    <p class="brand-kicker mb-1">防災管理システム</p>
                    <h1 class="brand-title mb-0">管理者</h1>
                </div>

                <nav class="nav nav-pills flex-column gap-2 mb-4">
                    <a href="index.php" class="nav-link active"><i class="bi bi-grid-1x2-fill me-2"></i>ダッシュボード</a>
                    <a href="register_list.php" class="nav-link"><i class="bi bi-people-fill me-2"></i>社員管理</a>
                    <a href="report_list.php" class="nav-link"><i class="bi bi-shield-check me-2"></i>安否報告</a>
                </nav>

                <div class="status-card mt-auto">
                    <p class="mb-2 small text-uppercase">システム状況</p>
                    <h6 class="mb-1">すべて正常に稼働中</h6>
                    <p class="small mb-0 opacity-75">最終更新: <span id="liveTime">--:--:--</span></p>
                </div>
            </aside>

            <main class="col-12 col-lg-9 col-xl-10 p-4 p-lg-4 p-xl-5 main-panel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <p class="text-muted mb-1">災害情報 管理パネル</p>
                        <h2 class="mb-0 fw-bold">安否報告フォーム</h2>
                        <?php if ($loggedInEmail !== ''): ?>
                            <p class="text-muted mb-0 mt-2 small">ログイン中: <?php echo h($loggedInEmail); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="report_list.php" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-1"></i>一覧へ
                        </a>
                        <a href="index.php" class="btn btn-outline-primary">
                            <i class="bi bi-house-door me-1"></i>ダッシュボード
                        </a>
                    </div>
                </div>

                <div class="panel-card">
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
                                    <input type="text" class="form-control" id="emp_no" value="<?php echo h($form['emp_no']); ?>" disabled >
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="name" class="form-label">名前</label>
                                    <input type="text" class="form-control" id="name" value="<?php echo h($form['name']); ?>" disabled>
                                </div>
                                <div class="col-12">
                                    <label for="deployment" class="form-label">部署</label>
                                    <input type="text" class="form-control" id="deployment" value="<?php echo h($form['deployment']); ?>" disabled>
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

            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>