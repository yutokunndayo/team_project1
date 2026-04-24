<?php
require_once 'conn.php';

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$flashMessage = '';
$flashType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = filter_var($_POST['delete_id'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);

    if ($deleteId === false) {
        $flashMessage = '削除対象が不正です。';
        $flashType = 'danger';
    } else {
        $stmt = $conn->prepare('DELETE FROM register WHERE id = ?');
        if ($stmt) {
            $stmt->bind_param('i', $deleteId);
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: ' . $_SERVER['PHP_SELF'] . '?status=deleted');
                exit;
            }

            $flashMessage = '削除に失敗しました。';
            $flashType = 'danger';
            $stmt->close();
        } else {
            $flashMessage = '削除処理を開始できませんでした。';
            $flashType = 'danger';
        }
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
    $flashMessage = '社員データを削除しました。';
}

$employees = [];
$result = $conn->query('SELECT id, `name`, email, `phone`, `deployment`, `position`, is_admin, created_at FROM register ORDER BY id DESC');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    $result->free();
} else {
    $flashMessage = '社員データの取得に失敗しました。';
    $flashType = 'danger';
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
    <title>社員一覧</title>
</head>

<body>
    <div class="dashboard-bg"></div>

    <div class="container-fluid">
        <div class="row min-vh-100">
            <aside class="col-12 col-lg-3 col-xl-2 sidebar-panel p-4 p-lg-3 p-xl-4">
                <div class="brand-box mb-4">
                    <p class="brand-kicker mb-1">防災管理システム</p>
                    <h1 class="brand-title mb-0">社員管理</h1>
                </div>

                <nav class="nav nav-pills flex-column gap-2 mb-4">
                    <a href="index.php" class="nav-link"><i class="bi bi-grid-1x2-fill me-2"></i>ダッシュボード</a>
                    <a href="register_list.php" class="nav-link active"><i class="bi bi-people-fill me-2"></i>社員一覧</a>
                    <a href="register.php" class="nav-link"><i class="bi bi-person-plus-fill me-2"></i>社員登録</a>
                    <a href="report_list.php" class="nav-link"><i class="bi bi-shield-check me-2"></i>安否報告</a>
                </nav>

                <div class="status-card mt-auto">
                    <p class="mb-2 small text-uppercase">社員件数</p>
                    <h6 class="mb-1"><?php echo h(count($employees)); ?>件</h6>
                    <p class="small mb-0 opacity-75">登録済みの社員データを表示しています。</p>
                </div>
            </aside>

            <main class="col-12 col-lg-9 col-xl-10 p-4 p-lg-4 p-xl-5 main-panel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <p class="text-muted mb-1">災害情報 管理パネル</p>
                        <h2 class="mb-0 fw-bold">社員一覧</h2>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="register.php" class="btn btn-primary">
                            <i class="bi bi-person-plus me-1"></i>新規登録
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-house-door me-1"></i>ダッシュボード
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right me-1"></i>ログアウト
                        </a>
                    </div>
                </div>

                <?php if ($flashMessage !== ''): ?>
                    <div class="alert alert-<?php echo h($flashType); ?>" role="alert">
                        <?php echo h($flashMessage); ?>
                    </div>
                <?php endif; ?>

                <section class="panel-card">
                    <div class="panel-head d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">登録済み社員</h5>
                        <span class="badge text-bg-light">全<?php echo h(count($employees)); ?>件</span>
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
                                    <th class="text-center">操作</th>
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
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="register.php?id=<?php echo h($employee['id']); ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil-square me-1"></i>編集
                                                    </a>
                                                    <form method="post" class="d-inline" onsubmit="return confirm('この社員を削除しますか？');">
                                                        <input type="hidden" name="delete_id" value="<?php echo h($employee['id']); ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash me-1"></i>削除
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">データがありません。</td>
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
    <script src="js/app.js"></script>
</body>

</html>