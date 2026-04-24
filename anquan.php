<?php
require_once 'conn.php';
function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}


$result = $conn->query("SELECT name, deployment, data, created_at FROM report ORDER BY created_at DESC");
$safety_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $safety_list[] = $row;
    }
}


$total = count($safety_list);
?>


<td class="job">社員</td>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>社員安全一覧</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="anquan.css">
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
                <a href="index.php" class="nav-link"><i class="bi bi-grid-1x2-fill me-2"></i>ダッシュボード</a>
                <a href="#" class="nav-link"><i class="bi bi-people-fill me-2"></i>社員管理</a>
                <a href="report.php" class="nav-link"><i class="bi bi-shield-check me-2"></i>安否報告</a>
                <a href="anquan.php" class="nav-link active"><i class="bi bi-list-check me-2"></i>安全一覧</a>
            </nav>
        </aside>


        <main class="col-12 col-lg-9 col-xl-10 p-4 p-lg-4 p-xl-5 main-panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0 fw-bold">社員安全一覧</h2>
                </div>
                <div class="date-chip">
                    <?php echo h(date('Y-m-d')); ?>
                </div>
            </div>

            <div class="table-wrap total-table">
                <table>
                    <thead>
                        <tr>
                            <th>安否</th>
                            <th>合計</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>該当者数:</td>
                            <td><?php echo $total; ?>名</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>

            <div class="table-wrap detail-table">
                <table>
                    <thead>
                        <tr>
                            <th>状況</th>
                            <th>日時</th>
                            <th>氏名</th>
                            <th>所属組織</th>
                            <th>役職</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($safety_list)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">データがありません</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($safety_list as $item): ?>
                                <tr>
                                    <td>
                                        <?php if ($item['data'] === '安全'): ?>
                                            <span class="icon safe">●</span>
                                        <?php else: ?>
                                            <span class="icon" style="color:red;">●</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="date"><?php echo h($item['created_at']); ?></td>
                                    <td class="name"><?php echo h($item['name']); ?></td>
                                    <td class="gray"><?php echo h($item['deployment']); ?></td>
                                    <td class="job"><?php echo h($item['position'] ?? '社員'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
</body>
</html>