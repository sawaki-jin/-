<?php include '../login_check.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>月影庵 - 商品リスト</title>
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
            max-width: 1000px;
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
        }
        .table thead {
            background-color: #343a40;
            color: white;
        }
        #genre {
            max-width: 200px;
        }
        .table td {
            vertical-align: middle;
            border-right: 1px solid #dee2e6; /* 縦線を追加 */
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
    </style>
</head>
<body>
    <?php
    include('../navbar.php');

    // データベース接続情報
    $dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
    $user = 'root';
    $password = ''; // パスワードがある場合はここに入力

    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ジャンルが選択された場合、そのジャンルに基づいてクエリを調整
        $genre = isset($_GET['genre']) ? $_GET['genre'] : null;
        if ($genre) {
            $sql = "SELECT * FROM shohin WHERE genre = :genre";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $sql = "SELECT * FROM shohin";
            $stmt = $pdo->query($sql);
        }
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
        exit();
    }
    ?>

    <div class="container mt-5">
        <h2><i class="fas fa-beer-mug-empty"></i> 商品リスト</h2>
        <form method="GET" action="" class="d-flex align-items-center mb-3"> <div class="form-group me-3"> 
                <select id="genre" name="genre" class="form-select">
                    <option value="">全ジャンル</option>
                    <option value="ビール" <?php if ($genre == 'ビール') echo 'selected'; ?>>1 - ビール</option>
                    <option value="日本酒" <?php if ($genre == '日本酒') echo 'selected'; ?>>2 - 日本酒</option>
                    <option value="焼酎" <?php if ($genre == '焼酎') echo 'selected'; ?>>3 - 焼酎</option>
                    <option value="ウィスキー" <?php if ($genre == 'ウィスキー') echo 'selected'; ?>>4 - ウィスキー</option>
                    <option value="サワー" <?php if ($genre == 'サワー') echo 'selected'; ?>>5 - サワー</option>
                    <option value="カクテル" <?php if ($genre == 'カクテル') echo 'selected'; ?>>6 - カクテル</option>
                    <option value="ソフトドリンク" <?php if ($genre == 'ソフトドリンク') echo 'selected'; ?>>7 - ソフトドリンク</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> 絞り込み</button>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 20%">商品名</th>
                        <th style="width: 15%">ジャンル</th>
                        <th style="width: 30%">商品説明</th>
                        <th style="width: 15%">価格</th>
                        <th style="width: 15%">原価</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $rowCount = $stmt->rowCount();
                        if ($rowCount == 0) {
                            echo "<tr><td colspan='5'>データが見つかりません。</td></tr>";
                        } else {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['genre']); ?></td>
                            <td><?php echo htmlspecialchars($row['details']); ?></td>
                            <td style="text-align: right;"><?php echo htmlspecialchars($row['price']); ?>円</td>
                            <td style="text-align: right;"><?php echo htmlspecialchars($row['cost_price']); ?>円</td>
                        </tr>
                    <?php 
                            endwhile; 
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <br>

    <?php
    $pdo = null; // データベース接続を閉じる
    include("../footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>