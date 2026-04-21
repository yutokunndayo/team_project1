<?php
require_once 'conn.php';

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$stats = [
    'total_employees' => 0,
    'admin_count' => 0,
    'total_reports' => 0,
    'safe_reports' => 0,
    'unsafe_reports' => 0,
];

$employees = [];
$reports = [];
$queryErrors = [];

$result = $conn->query("SELECT COUNT(*) AS total_employees, SUM(is_admin = 1) AS admin_count FROM register");
if ($result) {
    $row = $result->fetch_assoc();
    $stats['total_employees'] = (int) ($row['total_employees'] ?? 0);
    $stats['admin_count'] = (int) ($row['admin_count'] ?? 0);
} else {
    $queryErrors[] = 'register テーブルの集計に失敗しました。';
}

$result = $conn->query("SELECT COUNT(*) AS total_reports, SUM(`data` = '安全') AS safe_reports, SUM(`data` = '安全じゃない') AS unsafe_reports FROM report");
if ($result) {
    $row = $result->fetch_assoc();
    $stats['total_reports'] = (int) ($row['total_reports'] ?? 0);
    $stats['safe_reports'] = (int) ($row['safe_reports'] ?? 0);
    $stats['unsafe_reports'] = (int) ($row['unsafe_reports'] ?? 0);
} else {
    $queryErrors[] = 'report テーブルの集計に失敗しました。';
}

$result = $conn->query("SELECT id, `name`, email, `phone`, `deployment`, `position`, is_admin, created_at FROM register ORDER BY id DESC LIMIT 8");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
} else {
    $queryErrors[] = '社員データの取得に失敗しました。';
}

$result = $conn->query("SELECT `emp_no`, `name`, `deployment`, `data`, created_at FROM report ORDER BY created_at DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
} else {
    $queryErrors[] = '安否報告データの取得に失敗しました。';
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/app.css">
    <title>管理ダッシュボード</title>
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
                    <a href="#" class="nav-link"><i class="bi bi-people-fill me-2"></i>社員管理</a>
                    <a href="report_detail.php" class="nav-link"><i class="bi bi-shield-check me-2"></i>安否報告</a>
                    <a href="#" class="nav-link"><i class="bi bi-gear-fill me-2"></i>設定</a>
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
                        <h2 class="mb-0 fw-bold">管理ダッシュボード</h2>
                    </div>
                    <div class="date-chip">
                        <i class="bi bi-calendar3 me-2"></i>
                        <?php echo h(date('Y-m-d')); ?>
                    </div>
                </div>

                <?php if (!empty($queryErrors)): ?>
                    <div class="alert alert-warning" role="alert">
                        <?php foreach ($queryErrors as $error): ?>
                            <div><?php echo h($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="metric-card metric-1">
                            <p class="metric-label">社員総数</p>
                            <h3 class="metric-value"><?php echo h($stats['total_employees']); ?></h3>
                            <p class="metric-note mb-0"><i class="bi bi-people me-1"></i>登録済み社員</p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="metric-card metric-2">
                            <p class="metric-label">管理者数</p>
                            <h3 class="metric-value"><?php echo h($stats['admin_count']); ?></h3>
                            <p class="metric-note mb-0"><i class="bi bi-person-badge me-1"></i>権限アカウント</p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="metric-card metric-3">
                            <p class="metric-label">安全報告数</p>
                            <h3 class="metric-value"><?php echo h($stats['safe_reports']); ?></h3>
                            <p class="metric-note mb-0"><i class="bi bi-check2-circle me-1"></i>安全確認済み</p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="metric-card metric-4">
                            <p class="metric-label">要対応報告数</p>
                            <h3 class="metric-value"><?php echo h($stats['unsafe_reports']); ?></h3>
                            <p class="metric-note mb-0"><i class="bi bi-exclamation-triangle me-1"></i>対応が必要</p>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-12 xl-col-8">
                        <section class="panel-card h-100">
                            <div class="panel-head d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">社員一覧</h5>
                                <span class="badge text-bg-light">最新8件</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>社員ID</th>
                                            <th>名前</th>
                                            <th>メール</th>
                                            <th>電話</th>
                                            <th>部署</th>
                                            <th>役職</th>
                                            <th>権限</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($employees)): ?>
                                            <?php foreach ($employees as $employee): ?>
                                                <tr>
                                                    <td><?php echo h($employee['id']); ?></td>
                                                    <td><?php echo h($employee['name']); ?></td>
                                                    <td><?php echo h($employee['email']); ?></td>
                                                    <td><?php echo h($employee['phone']); ?></td>
                                                    <td><?php echo h($employee['deployment']); ?></td>
                                                    <td><?php echo h($employee['position']); ?></td>
                                                    <td>
                                                        <?php if ((int) $employee['is_admin'] === 1): ?>
                                                            <span class="badge rounded-pill text-bg-danger">管理者</span>
                                                        <?php else: ?>
                                                            <span class="badge rounded-pill text-bg-secondary">一般</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">データがありません。</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>

                    <div class="col-12 xl-col-4">
                        <section class="panel-card h-100">
                            <div class="panel-head d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">最新 安否報告</h5>
                                <span class="badge text-bg-light"><?php echo h($stats['total_reports']); ?>件</span>
                            </div>
                            <div class="report-feed">
                                <?php if (!empty($reports)): ?>
                                    <?php foreach ($reports as $report): ?>
                                        <div class="report-item">
                                            <div class="d-flex justify-content-between gap-2">
                                                <h6 class="mb-1"><?php echo h($report['name']); ?></h6>
                                                <?php if ($report['data'] === '安全'): ?>
                                                    <span class="badge rounded-pill text-bg-success">安全</span>
                                                <?php else: ?>
                                                    <span class="badge rounded-pill text-bg-warning">安全じゃない</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="small mb-1 text-muted">社員番号: <?php echo h($report['emp_no']); ?> / <?php echo h($report['deployment']); ?></p>
                                            <p class="small mb-0 text-muted"><?php echo h($report['created_at']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted mb-0">まだ報告がありません。</p>
                                <?php endif; ?>
                            </div>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>

</html>