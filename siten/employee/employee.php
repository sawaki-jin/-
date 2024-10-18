<?php include '../login_check.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>所属店舗従業員一覧</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body {
            background-color: #fff;
        }
        .navbar {
            background-color: #343a40;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 30px;
            width: 1000px;
        }
        h2 {
            color: #343a40;
            border-bottom: 2px solid #343a40;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .table {
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            width: 100%;
            table-layout: fixed;
        }
        .table thead {
            background-color: #343a40;
            color: white;
        }
        .table td {
            vertical-align: middle;
            border-right: 1px solid #dee2e6;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .table td:last-child {
            border-right: none;
        }
        .table thead th {
            border-right: 1px solid #dee2e6;
        }
        .table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }
        .form-inline {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .form-inline input {
            margin-right: 10px;
            max-width: 200px;
        }
        .btn-group {
            display: flex;
            gap: 5px;
        }
        .form-inline .btn {
            margin-right: 0;
        }
        .search-newlogin-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .search-newlogin-area .form-inline {
            flex-grow: 1;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php include('../navbar.php'); ?>

    <div class="container mt-5">
        <h2><i class="fas fa-users"></i> 所属店舗従業員一覧</h2>

        <div class="search-newlogin-area">
            <form class="form-inline" method="GET">
                <input type="text" name="search_employee" class="form-control" placeholder="従業員名で検索" value="<?php echo isset($_GET['search_employee']) ? htmlspecialchars($_GET['search_employee']) : ''; ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
            </form>
        </div>

        <?php
        try {
            $dsn = "mysql:dbname=bbadb;host=localhost;charset=utf8mb4";
            $username = "root";
            $password = "";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $pdo = new PDO($dsn, $username, $password, $options);

            $message = '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['ep_no']) && isset($_POST['new_password'])) {
                    $ep_no = $_POST['ep_no'];
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                    $get_name_sql = "SELECT ep_name FROM employee WHERE ep_no = :ep_no";
                    $get_name_stmt = $pdo->prepare($get_name_sql);
                    $get_name_stmt->execute([':ep_no' => $ep_no]);
                    $employee = $get_name_stmt->fetch();

                    if ($employee) {
                        $ep_name = $employee['ep_name'];

                        $update_sql = "UPDATE employee SET password = :new_password WHERE ep_no = :ep_no";
                        $update_stmt = $pdo->prepare($update_sql);
                        $update_stmt->execute([':new_password' => $new_password, ':ep_no' => $ep_no]);

                        $message = "<div class='alert alert-success' role='alert'>パスワードが更新されました: {$ep_name}</div>";
                    }
                } elseif (isset($_POST['delete_ep_no'])) {
                    $delete_ep_no = $_POST['delete_ep_no'];

                    $get_name_sql = "SELECT ep_name FROM employee WHERE ep_no = :ep_no";
                    $get_name_stmt = $pdo->prepare($get_name_sql);
                    $get_name_stmt->execute([':ep_no' => $delete_ep_no]);
                    $employee = $get_name_stmt->fetch();

                    if ($employee) {
                        $ep_name = $employee['ep_name'];

                        $delete_sql = "DELETE FROM employee WHERE ep_no = :delete_ep_no";
                        $delete_stmt = $pdo->prepare($delete_sql);
                        $delete_stmt->execute([':delete_ep_no' => $delete_ep_no]);

                        $message = "<div class='alert alert-success' role='alert'>従業員が削除されました: {$ep_name}</div>";
                    }
                }
            }

            if (isset($_SESSION['tenpo_no'])) {
                $tenpo_no = $_SESSION['tenpo_no'];

                $search_employee = isset($_GET['search_employee']) ? $_GET['search_employee'] : '';
                $sql = "SELECT ep_no, ep_name, tenpo_no, yakusyoku, password FROM employee WHERE tenpo_no = :tenpo_no";
                if ($search_employee) {
                    $sql .= " AND ep_name LIKE :search_employee";
                }
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':tenpo_no', $tenpo_no, PDO::PARAM_INT);
                if ($search_employee) {
                    $stmt->bindValue(':search_employee', '%' . $search_employee . '%', PDO::PARAM_STR);
                }
                $stmt->execute();

                echo $message;

                echo "<div class='table-responsive'>
                        <table class='table table-hover'>
                            <thead>
                                <tr>
                                    <th style='width: 15%;'>従業員番号</th>
                                    <th style='width: 20%;'>従業員名</th>
                                    <th style='width: 15%;'>店舗番号</th>
                                    <th style='width: 15%;'>役職</th>
                                    <th style='width: 35%;'>操作</th>
                                </tr>
                            </thead>
                            <tbody>";

                while ($row = $stmt->fetch()) {
                    echo "<tr>
                            <td>{$row['ep_no']}</td>
                            <td>{$row['ep_name']}</td>
                            <td>{$row['tenpo_no']}</td>
                            <td>{$row['yakusyoku']}</td>
                            <td>
                                <div class='btn-group'>
                                    <form method='POST' class='form-inline'>
                                        <input type='hidden' name='ep_no' value='{$row['ep_no']}'>
                                        <input type='password' name='new_password' class='form-control' placeholder='新しいパスワード'>
                                        <button type='submit' class='btn btn-primary btn-sm'><i class='fas fa-key'></i> 変更</button>
                                    </form>
                                    <form method='POST' class='form-inline'>
                                        <input type='hidden' name='delete_ep_no' value='{$row['ep_no']}'>
                                        <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"本当に削除しますか？\")'><i class='fas fa-trash-alt'></i> 削除</button>
                                    </form>
                                </div>
                            </td>
                          </tr>";
                }

                echo "</tbody></table></div>";
            } else {
                echo "<div class='alert alert-danger' role='alert'>店舗番号が設定されていません。ログインしてください。</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger' role='alert'>接続に失敗しました: " . $e->getMessage() . "</div>";
        }
        ?>
    </div>
    <br>

    <?php include("../footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>