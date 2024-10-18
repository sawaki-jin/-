<?php include '../navbar.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ニュースタイトル一覧</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .table tbody tr td a.hover-sample:hover {
            color: orange;
            font-size: 1.1em;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <h2><i class="fas fa-newspaper"></i> ニュースタイトル一覧</h2>

        <div class="search-newlogin-area">
            <form class="form-inline" method="GET">
                <input type="text" name="search_title" class="form-control" placeholder="タイトルで検索" value="<?php echo isset($_GET['search_title']) ? htmlspecialchars($_GET['search_title']) : ''; ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
            </form>
            <a href="news_post.php" class="btn btn-success"><i class="fas fa-plus"></i> <span style="color: white;">投稿する</span></a>
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

            // 削除処理
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_article_id'])) {
                $delete_article_id = intval($_POST['delete_article_id']);
                $sql = "DELETE FROM news_articles WHERE article_id = :article_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':article_id', $delete_article_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            // 検索クエリの処理
            $search_title = isset($_GET['search_title']) ? $_GET['search_title'] : '';
            $sql = "SELECT article_id, title, store, DATE_FORMAT(created_at, '%Y-%m-%d') as created_date 
                    FROM news_articles";
            if ($search_title) {
                $sql .= " WHERE title LIKE :search_title";
            }
            $sql .= " ORDER BY article_id DESC";
            $stmt = $pdo->prepare($sql);
            if ($search_title) {
                $stmt->bindValue(':search_title', '%' . $search_title . '%');
            }
            $stmt->execute();

            // 今日の日付を取得
            $today = date('Y-m-d');

            ?>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 13%;">日付</th> <!-- 日付欄の幅を少し広げる -->
                            <th style="width: 15%;">店舗</th>
                            <th style="width: 47%;">タイトル</th>
                            <th style="width: 25%;">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rowCount = $stmt->rowCount();
                        if ($rowCount == 0) {
                            echo "<tr><td colspan='4'>データが見つかりません。</td></tr>";
                        } else {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                                $article_id = htmlspecialchars($row['article_id'], ENT_QUOTES, 'UTF-8');
                                $titleText = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
                                $store = htmlspecialchars($row['store'], ENT_QUOTES, 'UTF-8');
                                $createdAt = htmlspecialchars($row['created_date'], ENT_QUOTES, 'UTF-8');
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
                            <td>
                                <div class='btn-group'>
                                    <a href='edit_news.php?article_id=<?php echo $article_id; ?>' class='btn btn-primary btn-sm' style='color:#fff;'><i class="fas fa-edit"></i> 編集</a>
                                    <form method='post' style='display:inline;'>
                                        <input type='hidden' name='delete_article_id' value='<?php echo $article_id; ?>'>
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