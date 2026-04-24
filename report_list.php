<?php

require_once 'conn.php';

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$status = $_GET['status'] ?? '';
$whereClause = '';
$pageTitle = '安否報告一覧';

if ($status === 'unsafe') {
    $whereClause = "WHERE `data` = '安全じゃない'";
    $pageTitle = '要対応報告一覧';
}

$result = $conn->query("SELECT emp_no, name, deployment, data, created_at FROM report {$whereClause} ORDER BY created_at DESC LIMIT 5");
$recentReports = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentReports[] = $row;
    }
    $result->free();
} else {
    die('データの取得に失敗しました。');
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
    <title><?php echo h($pageTitle); ?></title>
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
                    <a href="index.php" class="nav-link "><i class="bi bi-grid-1x2-fill me-2"></i>ダッシュボード</a>
                    <a href="register_list.php" class="nav-link"><i class="bi bi-people-fill me-2"></i>社員管理</a>
                    <a href="report_list.php" class="nav-link active"><i class="bi bi-shield-check me-2"></i>安否報告</a>
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
                        <h2 class="mb-0 fw-bold"><?php echo h($pageTitle); ?></h2>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="report.php" class="btn btn-outline-primary">
                            <i class="bi bi-pencil-square me-1"></i>報告入力へ
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-house-door me-1"></i>ダッシュボード
                        </a>
                    </div>
                </div>

                <section class="panel-card">
                    <div class="panel-head d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><?php echo $status === 'unsafe' ? '要対応の報告' : '最新の報告'; ?></h5>
                        <span class="badge text-bg-light"><?php echo h(count($recentReports)); ?>件</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>日時</th>
                                    <th>社員番号</th>
                                    <th>名前</th>
                                    <th>部署</th>
                                    <th>状況</th>
                                    <th>詳しく</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentReports)): ?>
                                    <?php foreach ($recentReports as $report): ?>
                                        <tr>
                                            <td><?php echo h($report['created_at']); ?></td>
                                            <td><?php echo h($report['emp_no']); ?></td>
                                            <td><?php echo h($report['name']); ?></td>
                                            <td><?php echo h($report['deployment']); ?></td>
                                            <td>
                                                <?php if ($report['data'] === '安全'): ?>
                                                    <span class="badge text-bg-success">安全</span>
                                                <?php else: ?>
                                                    <span class="badge text-bg-warning">安全じゃない</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="report_detail.php?emp_no=<?php echo urlencode($report['emp_no']); ?>" class="btn btn-sm btn-outline-info">詳細</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">報告データがありません。</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>