<?php include '../login_check.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>月影庵</title>
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
            width: 100%; /* テーブルの幅を親要素に合わせる */
            table-layout: fixed; /* 各列の幅を均等にする */
        }
        .table thead {
            background-color: #343a40;
            color: white;
        }
        .table td {
            vertical-align: middle;
            border-right: 1px solid #dee2e6; /* 縦線を追加 */
            overflow: hidden; /* セルの内容がはみ出ないようにする */
            text-overflow: ellipsis; /* はみ出した部分を省略記号(...)で表示 */
            white-space: nowrap; /* セル内での折り返しを防ぐ */
        }
        .table td:last-child { /* 最後の列の縦線を消す */
            border-right: none;
        }
        /* 縦線をth,trまで伸ばす */
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
        /* 追加スタイル */
        .form-inline .btn {
            margin-right: 0; /* ボタンの右側の余白を削除 */
        }

        /* 検索フォームと新規ログインボタンのスタイル */
        .search-newlogin-area {
            display: flex;
            justify-content: space-between; /* 要素を両端に配置 */
            align-items: center; /* 垂直方向に中央揃え */
            margin-bottom: 10px;
        }
        .search-newlogin-area .form-inline {
            flex-grow: 1; /* 検索フォームに余ったスペースを充てる */
            margin-right: 10px; /* 新規ログインボタンとの間隔 */
        }
        /* 新規ログインボタンのスタイル */
        .btn-success {
            background-color: #28a745; /* 緑色のカラーコード */
            border-color: #28a745; /* 緑色の境界線の色 */
        }
        .btn-success:hover {
            background-color: #218838; /* ホバー時の少し濃い緑色 */
            border-color: #1e7e34; /* ホバー時の少し濃い緑色の境界線の色 */
        }
        .btn-success:focus, .btn-success.focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5); /* 緑色のフォーカス時の影 */
        }

        .btn-success:not(:disabled):not(.disabled):active, .btn-success:not(:disabled):not(.disabled).active,
        .show > .btn-success.dropdown-toggle {
            background-color: #218838; /* アクティブ時の少し濃い緑色 */
            border-color: #1e7e34; /* アクティブ時の少し濃い緑色の境界線の色 */
        }
        .btn-success:not(:disabled):not(.disabled):active:focus, .btn-success:not(:disabled):not(.disabled).active:focus,
        .show > .btn-success.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5); /* 緑色のアクティブ時の影 */
        }
        /* 新規ログインボタンのアイコンのスタイル */
        .btn-success i {
            color: white; /* アイコンの色を白に設定 */
        }
    </style>
</head>
<body>
    <?php
    include('../navbar.php');
    ?>

    <div class="container mt-5">
        <h2><i class="fas fa-users"></i> 全店舗従業員一覧</h2>

        <!-- 検索フォームと新規ログインボタン -->
        <div class="search-newlogin-area">
            <form class="form-inline" method="GET">
                <input type="text" name="search_employee" class="form-control" placeholder="従業員名で検索" value="<?php echo isset($_GET['search_employee']) ? htmlspecialchars($_GET['search_employee']) : ''; ?>">
                <input type="text" name="search_tenpo" class="form-control" placeholder="店舗名で検索" value="<?php echo isset($_GET['search_tenpo']) ? htmlspecialchars($_GET['search_tenpo']) : ''; ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
            </form>
            <a href="new_login.php" class="btn btn-success"><i class="fas fa-user-plus"></i> <span style="color: white;">新規ログイン</span></a>
        </div>

        <?php
        // データベース接続情報
        $dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
        $user = 'root';
        $password = ''; // パスワードがある場合はここに入力

        try {
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // メッセージ変数の初期化
            $message = '';

            // パスワード変更処理
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['ep_no']) && isset($_POST['new_password'])) {
                    $ep_no = $_POST['ep_no'];
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                    // 従業員名を取得
                    $get_name_sql = "SELECT ep_name FROM employee WHERE ep_no = :ep_no";
                    $get_name_stmt = $pdo->prepare($get_name_sql);
                    $get_name_stmt->execute([':ep_no' => $ep_no]);
                    $employee = $get_name_stmt->fetch();

                    if ($employee) {
                        $ep_name = $employee['ep_name'];

                        // パスワードを更新するSQLクエリ
                        $update_sql = "UPDATE employee SET password = :new_password WHERE ep_no = :ep_no";
                        $update_stmt = $pdo->prepare($update_sql);
                        $update_stmt->execute([':new_password' => $new_password, ':ep_no' => $ep_no]);

                        $message = "<div class='alert alert-success' role='alert'>パスワードが更新されました: {$ep_name}</div>";
                    }
                } elseif (isset($_POST['delete_ep_no'])) {
                    $delete_ep_no = $_POST['delete_ep_no'];

                    // 従業員名を取得
                    $get_name_sql = "SELECT ep_name FROM employee WHERE ep_no = :ep_no";
                    $get_name_stmt = $pdo->prepare($get_name_sql);
                    $get_name_stmt->execute([':ep_no' => $delete_ep_no]);
                    $employee = $get_name_stmt->fetch();

                    if ($employee) {
                        $ep_name = $employee['ep_name'];

                        // 従業員を削除するSQLクエリ
                        $delete_sql = "DELETE FROM employee WHERE ep_no = :delete_ep_no";
                        $delete_stmt = $pdo->prepare($delete_sql);
                        $delete_stmt->execute([':delete_ep_no' => $delete_ep_no]);

                        $message = "<div class='alert alert-success' role='alert'>従業員が削除されました: {$ep_name}</div>";
                    }
                }
            }

            // 検索クエリの処理
            $search_employee = isset($_GET['search_employee']) ? $_GET['search_employee'] : '';
            $search_tenpo = isset($_GET['search_tenpo']) ? $_GET['search_tenpo'] : '';
            $sql = "SELECT e.ep_no, e.ep_name, t.tenpo_name, e.yakusyoku 
                    FROM employee e 
                    JOIN tenpo t ON e.tenpo_no = t.tenpo_no";
            if ($search_employee || $search_tenpo) {
                $sql .= " WHERE";
                $conditions = [];
                if ($search_employee) {
                    $conditions[] = "e.ep_name LIKE :search_employee";
                }
                if ($search_tenpo) {
                    $conditions[] = "t.tenpo_name LIKE :search_tenpo";
                }
                $sql .= " " . implode(" AND ", $conditions);
            }
            $stmt = $pdo->prepare($sql);
            if ($search_employee) {
                $stmt->bindValue(':search_employee', '%' . $search_employee . '%');
            }
            if ($search_tenpo) {
                $stmt->bindValue(':search_tenpo', '%' . $search_tenpo . '%');
            }
            $stmt->execute();

            // メッセージを表示
            echo $message;
            ?>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10%;">従業員番号</th>
                            <th style="width: 15%;">従業員名</th>
                            <th style="width: 20%;">店舗名</th>
                            <th style="width: 15%;">役職</th>
                            <th style="width: 40%;">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rowCount = $stmt->rowCount();
                        if ($rowCount == 0) {
                            echo "<tr><td colspan='6'>データが見つかりません。</td></tr>";
                        } else {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['ep_no']); ?></td>
                            <td><?php echo htmlspecialchars($row['ep_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['tenpo_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['yakusyoku']); ?></td>
                            <td>
                                <div class='btn-group'>
                                    <form method='POST' class='form-inline'>
                                        <input type='hidden' name='ep_no' value='<?php echo $row['ep_no']; ?>'>
                                        <input type='password' name='new_password' class='form-control' placeholder='新しいパスワード'>
                                        <button type='submit' class='btn btn-primary btn-sm'><i class="fas fa-key"></i> 変更</button>
                                    </form>
                                    <form method='POST' class='form-inline'>
                                        <input type='hidden' name='delete_ep_no' value='<?php echo $row['ep_no']; ?>'>
                                        <button type='submit' class='btn btn-danger btn-sm' onclick="return confirm('本当に削除しますか？')"><i class="fas fa-trash-alt"></i> 削除</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php
        } catch (PDOException $e) {
            echo "エラー: " . $e->getMessage();
            exit();
        }
        $pdo = null; // データベース接続を閉じる
        ?>
    </div>
    <br>

    <?php
    include("../footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>