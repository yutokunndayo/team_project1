<?php
require_once 'conn.php';

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$employee = [
    'id' => '',
    'name' => '',
    'email' => '',
    'phone' => '',
    'deployment' => '',
    'position' => '',
    'hiring_date' => '',
    'date_of_birth' => '',
    'address' => '',
    'is_admin' => 0,
    'password' => '',
    'con_password' => '',
];

$errors = [];
$isEdit = false;

if (isset($_GET['id'])) {
    $editId = filter_var($_GET['id'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);

    if ($editId !== false) {
        $stmt = $conn->prepare('SELECT id, `name`, email, `phone`, `deployment`, `position`, hiring_date, date_of_birth, address, is_admin, password, con_password FROM register WHERE id = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('i', $editId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            if ($row) {
                $employee = array_merge($employee, $row);
                $isEdit = true;
            } else {
                $errors[] = '指定された社員が見つかりませんでした。';
            }
            $stmt->close();
        } else {
            $errors[] = '社員データの読み込みに失敗しました。';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedId = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);

    $employee['id'] = $postedId !== false ? $postedId : '';
    $employee['name'] = trim($_POST['name'] ?? '');
    $employee['email'] = trim($_POST['email'] ?? '');
    $employee['phone'] = trim($_POST['phone'] ?? '');
    $employee['deployment'] = trim($_POST['deployment'] ?? '');
    $employee['position'] = trim($_POST['position'] ?? '');
    $employee['hiring_date'] = trim($_POST['hiring_date'] ?? '');
    $employee['date_of_birth'] = trim($_POST['date_of_birth'] ?? '');
    $employee['address'] = trim($_POST['address'] ?? '');
    $employee['is_admin'] = isset($_POST['is_admin']) ? 1 : 0;
    $employee['password'] = trim($_POST['password'] ?? '');
    $employee['con_password'] = trim($_POST['con_password'] ?? '');

    $isEdit = $employee['id'] !== '';

    foreach (['name', 'email', 'phone', 'deployment', 'position', 'hiring_date', 'date_of_birth', 'address'] as $field) {
        if ($employee[$field] === '') {
            $errors[] = '必須項目をすべて入力してください。';
            break;
        }
    }

    if (!$isEdit || $employee['password'] !== '' || $employee['con_password'] !== '') {
        if ($employee['password'] === '' || $employee['con_password'] === '') {
            $errors[] = 'パスワードと確認用パスワードを入力してください。';
        } elseif ($employee['password'] !== $employee['con_password']) {
            $errors[] = 'パスワードと確認用パスワードが一致しません。';
        }
    }

    if (empty($errors)) {
        if ($isEdit) {
            if ($employee['password'] === '' && $employee['con_password'] === '') {
                $existingStmt = $conn->prepare('SELECT password, con_password FROM register WHERE id = ? LIMIT 1');
                if ($existingStmt) {
                    $existingStmt->bind_param('i', $employee['id']);
                    $existingStmt->execute();
                    $existingResult = $existingStmt->get_result();
                    $existingRow = $existingResult ? $existingResult->fetch_assoc() : null;
                    $existingStmt->close();

                    if ($existingRow) {
                        $employee['password'] = $existingRow['password'];
                        $employee['con_password'] = $existingRow['con_password'];
                    }
                }
            }

            $stmt = $conn->prepare('UPDATE register SET `name` = ?, email = ?, password = ?, con_password = ?, `phone` = ?, deployment = ?, position = ?, hiring_date = ?, date_of_birth = ?, address = ?, is_admin = ? WHERE id = ?');
            if ($stmt) {
                $stmt->bind_param(
                    'ssssisssssii',
                    $employee['name'],
                    $employee['email'],
                    $employee['password'],
                    $employee['con_password'],
                    $employee['phone'],
                    $employee['deployment'],
                    $employee['position'],
                    $employee['hiring_date'],
                    $employee['date_of_birth'],
                    $employee['address'],
                    $employee['is_admin'],
                    $employee['id']
                );

                if ($stmt->execute()) {
                    $stmt->close();
                    header('Location: register_list.php?status=saved');
                    exit;
                }

                $errors[] = '社員情報の更新に失敗しました。';
                $stmt->close();
            } else {
                $errors[] = '更新処理を開始できませんでした。';
            }
        } else {
            $stmt = $conn->prepare('INSERT INTO register (`name`, email, password, con_password, `phone`, deployment, position, hiring_date, date_of_birth, address, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            if ($stmt) {
                $stmt->bind_param(
                    'ssssisssssi',
                    $employee['name'],
                    $employee['email'],
                    $employee['password'],
                    $employee['con_password'],
                    $employee['phone'],
                    $employee['deployment'],
                    $employee['position'],
                    $employee['hiring_date'],
                    $employee['date_of_birth'],
                    $employee['address'],
                    $employee['is_admin']
                );

                if ($stmt->execute()) {
                    $stmt->close();
                    header('Location: register_list.php?status=saved');
                    exit;
                }

                $errors[] = '社員情報の登録に失敗しました。';
                $stmt->close();
            } else {
                $errors[] = '登録処理を開始できませんでした。';
            }
        }
    }
}

$pageTitle = $isEdit ? '社員編集' : '社員登録';
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
    <title><?php echo h($pageTitle); ?></title>
</head>

<body>
    <div class="dashboard-bg"></div>

    <div class="container-fluid">
        <div class="row min-vh-100">
            <aside class="col-12 col-lg-3 col-xl-2 sidebar-panel p-4 p-lg-3 p-xl-4">
                <div class="brand-box mb-4">
                    <p class="brand-kicker mb-1">防災管理システム</p>
                    <h1 class="brand-title mb-0"><?php echo h($pageTitle); ?></h1>
                </div>

                <nav class="nav nav-pills flex-column gap-2 mb-4">
                    <a href="index.php" class="nav-link"><i class="bi bi-grid-1x2-fill me-2"></i>ダッシュボード</a>
                    <a href="register_list.php" class="nav-link active"><i class="bi bi-people-fill me-2"></i>社員管理</a>
                    <a href="report_list.php" class="nav-link"><i class="bi bi-shield-check me-2"></i>安否報告</a>
                </nav>

                <div class="status-card mt-auto">
                    <p class="mb-2 small text-uppercase">入力状況</p>
                    <h6 class="mb-1"><?php echo $isEdit ? '社員情報を編集' : '社員情報を登録'; ?></h6>
                    <p class="small mb-0 opacity-75">必要な項目を入力して保存してください。</p>
                </div>
            </aside>

            <main class="col-12 col-lg-9 col-xl-10 p-4 p-lg-4 p-xl-5 main-panel">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                    <div>
                        <p class="text-muted mb-1">災害情報 管理パネル</p>
                        <h2 class="mb-0 fw-bold"><?php echo h($pageTitle); ?></h2>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="register_list.php" class="btn btn-outline-secondary">
                            <i class="bi bi-list-ul me-1"></i>一覧へ戻る
                        </a>
                        <a href="index.php" class="btn btn-outline-primary">
                            <i class="bi bi-house-door me-1"></i>ダッシュボード
                        </a>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo h($error); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <section class="panel-card">
                    <form method="post" action="<?php echo h($_SERVER['PHP_SELF']); ?>">
                        <input type="hidden" name="id" value="<?php echo h($employee['id']); ?>">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">名前</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?php echo h($employee['name']); ?>" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">メール</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?php echo h($employee['email']); ?>" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label">パスワード</label>
                                <input type="password" id="password" name="password" class="form-control" value="<?php echo h($employee['password']); ?>" <?php echo $isEdit ? '' : 'required'; ?>>
                                <div class="form-text"><?php echo $isEdit ? '未入力のままだと現在のパスワードを保持します。' : '新規登録では必須です。'; ?></div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="con_password" class="form-label">確認用パスワード</label>
                                <input type="password" id="con_password" name="con_password" class="form-control" value="<?php echo h($employee['con_password']); ?>" <?php echo $isEdit ? '' : 'required'; ?>>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="phone" class="form-label">電話</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="<?php echo h($employee['phone']); ?>" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="deployment" class="form-label">部署</label>
                                <input type="text" id="deployment" name="deployment" class="form-control" value="<?php echo h($employee['deployment']); ?>" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="position" class="form-label">役職</label>
                                <input type="text" id="position" name="position" class="form-control" value="<?php echo h($employee['position']); ?>" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="hiring_date" class="form-label">入社日</label>
                                <input type="date" id="hiring_date" name="hiring_date" class="form-control" value="<?php echo h($employee['hiring_date']); ?>" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="date_of_birth" class="form-label">生年月日</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="<?php echo h($employee['date_of_birth']); ?>" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="address" class="form-label">住所</label>
                                <input type="text" id="address" name="address" class="form-control" value="<?php echo h($employee['address']); ?>" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" value="1" <?php echo (int) $employee['is_admin'] === 1 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_admin">管理者権限</label>
                                </div>
                            </div>
                            <div class="col-12 d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i><?php echo $isEdit ? '更新する' : '登録する'; ?>
                                </button>
                                <a href="register_list.php" class="btn btn-outline-secondary">キャンセル</a>
                            </div>
                        </div>
                    </form>
                </section>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>