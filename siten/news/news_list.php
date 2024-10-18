<?php
include '../login_check.php';

// データベース接続情報
try {
    $dsn = "mysql:dbname=bbadb;host=localhost;charset=utf8mb4";
    $username = "root";
    $password = ""; // パスワードが必要であればここに設定
    $options = [];
    $pdo = new PDO($dsn, $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $msg = 'データベース接続に失敗しました: ' . $e->getMessage();
    echo "<div class='login-page'><div class='form'><h1>$msg</h1></div></div>";
    exit;
}

// 検索クエリの処理
$search_title = isset($_GET['search_title']) ? $_GET['search_title'] : '';
$sql = "SELECT article_id, title, store, DATE_FORMAT(created_at, '%Y-%m-%d') as created_date FROM news_articles";
if ($search_title) {
    $sql .= " WHERE title LIKE :search_title";
}
$sql .= " ORDER BY article_id DESC";
$stmt = $pdo->prepare($sql);
if ($search_title) {
    $stmt->bindValue(':search_title', '%' . $search_title . '%');
}
$stmt->execute();
$titles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 今日の日付を取得
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>ニュースタイトル一覧</title>
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
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-success:focus, .btn-success.focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5);
        }
        .btn-success:not(:disabled):not(.disabled):active, .btn-success:not(:disabled):not(.disabled).active,
        .show > .btn-success.dropdown-toggle {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-success:not(:disabled):not(.disabled):active:focus, .btn-success:not(:disabled):not(.disabled).active:focus,
        .show > .btn-success.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5);
        }
        .btn-success i {
            color: white;
        }
        .new-flag {
            color: red;
            font-weight: bold;
            margin-left: 10px;
        }
        /* 追記：ホバー時のスタイル */
        .table tbody tr:hover {
            background-color: #f2f2f2;
        }
        .table tbody tr td a.hover-sample {
            color: #6c757d; /* 灰色 */
        }
        .table tbody tr td a.hover-sample:hover {
            color: orange;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>
    <div class="container mt-5">
        <h2><i class="fas fa-newspaper"></i> ニュースタイトル一覧</h2>

        <div class="search-newlogin-area">
            <form class="form-inline" method="GET">
                <input type="text" name="search_title" class="form-control" placeholder="タイトルで検索" value="<?php echo isset($_GET['search_title']) ? htmlspecialchars($_GET['search_title']) : ''; ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 20%;">日付</th>
                        <th style="width: 20%;">店舗</th>
                        <th style="width: 60%;">タイトル</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($titles) {
                        foreach ($titles as $title) {
                            $article_id = htmlspecialchars($title['article_id'], ENT_QUOTES, 'UTF-8');
                            $titleText = htmlspecialchars($title['title'], ENT_QUOTES, 'UTF-8');
                            $store = htmlspecialchars($title['store'], ENT_QUOTES, 'UTF-8');
                            $createdAt = htmlspecialchars($title['created_date'], ENT_QUOTES, 'UTF-8');
                            $storeClass = (strpos($store, '本社') !== false) ? 'main-office' : '';
                            $newFlag = ($createdAt === $today) ? "<span class='new-flag'>new‼</span>" : "";
                    ?>
                    <tr>
                        <td><?php echo $createdAt; ?></td>
                        <td><?php echo $store; ?></td>
                        <td>
                            <a class='hover-sample' style='text-decoration:none;' href='news_details.php?article_id=<?php echo $article_id; ?>'>
                                <?php echo $titleText . " " . $newFlag; ?>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='3'>ニュースタイトルがありません。</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
