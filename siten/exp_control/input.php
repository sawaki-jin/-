<?php
session_start();

// データベース接続情報
$dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
$user = 'root';
$password = '';

try {
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // 商品情報を取得
    $stmt1 = $db->prepare("
        SELECT * FROM shohin
    ");
    $stmt1->execute();
} catch (PDOException $e) {
    exit('エラー：' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>売上入力</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="css/input.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
            width: 800px;
        }
        h2 {
            color: #343a40;
            border-bottom: 2px solid #343a40;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        h2 i {
            margin-right: 10px;
            font-size: 1.5em;
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
    </style>
</head>
<body>
    <?php include('../navbar.php'); ?>

    <div class="container mt-5">
        <h2><i class="fas fa-cash-register"></i>売上入力</h2>

            <form method="post" action="input2.php">
                <table class="table">
                    <thead>
                        <tr>
                            <th>商品名</th>
                            <th>ジャンル</th>
                            <th>数量入力</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt1->fetch()): ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="shohin_no[]" value="<?php echo $row['shohin_no'] ?>">
                                    <?php echo $row['name'] ?>
                                </td>
                                <td><?php echo $row['genre'] ?></td>
                                <td>
                                    <input type="number" name="kazu[]" id="<?php echo $row['shohin_no'] ?>" min="0" required>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <input type="submit" value="送信" class="btn btn-primary">
            </form>
    </div>
    <br>
    <?php include("../footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>