<?php
require_once 'conn.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loggedInUserId = filter_var($_SESSION['id'] ?? '', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
$isAdmin = (int) ($_SESSION['is_admin'] ?? 0) === 1;

if ($loggedInUserId !== false) {
    $roleStmt = $conn->prepare('SELECT is_admin FROM register WHERE id = ? LIMIT 1');
    if ($roleStmt) {
        $roleStmt->bind_param('i', $loggedInUserId);
        $roleStmt->execute();
        $roleResult = $roleStmt->get_result();
        $roleRow = $roleResult ? $roleResult->fetch_assoc() : null;
        if ($roleRow) {
            $isAdmin = (int) ($roleRow['is_admin'] ?? 0) === 1;
            $_SESSION['is_admin'] = $isAdmin ? 1 : 0;
        }
        $roleStmt->close();
    }
}


function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$detail = null;
$queryError = '';

$recordId = trim($_GET['emp_no'] ?? ($_GET['id'] ?? ''));

if ($recordId === '' || !ctype_digit($recordId)) {
    $queryError = 'URLに emp_no か id を指定してください。例: report_detail.php?emp_no=4';
} else {
    $sql = 'SELECT emp_no, name, deployment, comment, data, created_at FROM report WHERE emp_no = ? ORDER BY created_at DESC LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        $empNo = (int) $recordId;
        mysqli_stmt_bind_param($stmt, 'i', $empNo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $detail = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$detail) {
            $queryError = '指定された社員番号の報告データが見つかりませんでした。';
        }
    } else {
        $queryError = '報告データの取得に失敗しました。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>報告詳細</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/app.css">
    <style>
        body {
            background: transparent;
        }

        .detail-card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(20, 37, 63, 0.08);
        }

        .detail-title {
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .label-box {
            font-size: 0.82rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .value-box {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .comment-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.9rem;
            min-height: 90px;
            white-space: pre-wrap;
        }
    </style>
</head>

<body>
    <div class="dashboard-bg"></div>

    <div class="container-fluid">
        <div class="row min-vh-100">

            <?php if ($isAdmin): ?>
                <aside class="col-12 col-lg-3 col-xl-2 sidebar-panel p-4 p-lg-3 p-xl-4">
                    <div class="brand-box mb-4">
                        <p class="brand-kicker mb-1">防災管理システム</p>
                        <h1 class="brand-title mb-0">管理者</h1>
                    </div>

                    <nav class="nav nav-pills flex-column gap-2 mb-4">
                        <a href="index.php" class="nav-link"><i class="bi bi-grid-1x2-fill me-2"></i>ダッシュボード</a>
                        <a href="register_list.php" class="nav-link"><i class="bi bi-people-fill me-2"></i>社員管理</a>
                        <a href="report_list.php" class="nav-link active"><i class="bi bi-shield-check me-2"></i>安否報告</a>
                    </nav>

                    <div class="status-card mt-auto">
                        <p class="mb-2 small text-uppercase">システム状況</p>
                        <h6 class="mb-1">すべて正常に稼働中</h6>
                        <p class="small mb-0 opacity-75">最終更新: <span id="liveTime">--:--:--</span></p>
                    </div>
                </aside>
            <?php endif; ?>

            <main class="<?php echo $isAdmin ? 'col-12 col-lg-9 col-xl-10' : 'col-12'; ?> p-4 p-lg-4 p-xl-5 main-panel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <p class="text-muted mb-1">災害情報 管理パネル</p>
                        <h2 class="mb-0 fw-bold">報告詳細</h2>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="report_list.php" class="btn btn-outline-secondary btn-sm">一覧へ戻る</a>
                        <?php if ($isAdmin): ?>
                            <a href="index.php" class="btn btn-outline-primary btn-sm">ダッシュボードへ戻る</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($queryError !== ''): ?>
                    <div class="alert alert-danger" role="alert"><?php echo h($queryError); ?></div>
                <?php endif; ?>

                <?php if ($detail): ?>
                    <div class="card detail-card">
                        <div class="card-body p-4 p-md-5">
                            <div class="row g-4 mb-2">
                                <div class="col-12 col-md-6">
                                    <div class="label-box">社員番号</div>
                                    <div class="value-box"><?php echo h($detail['emp_no']); ?></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="label-box">名前</div>
                                    <div class="value-box"><?php echo h($detail['name']); ?></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="label-box">部署</div>
                                    <div class="value-box"><?php echo h($detail['deployment']); ?></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="label-box">状況</div>
                                    <div class="value-box">
                                        <?php if ($detail['data'] === '安全'): ?>
                                            <span class="badge text-bg-success">安全</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-warning">安全じゃない</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="label-box">コメント</div>
                                    <div class="comment-box"><?php echo h($detail['comment']); ?></div>
                                </div>
                                <div class="col-12">
                                    <div class="label-box">作成日時</div>
                                    <div class="value-box"><?php echo h($detail['created_at']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>