<?php

require_once 'conn.php';

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">最新の報告</h2>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>日時</th>
                                        <th>社員番号</th>
                                        <th>名前</th>
                                        <th>部署</th>
                                        <th>状況</th>
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
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">報告データがありません。</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
</body>
</html>