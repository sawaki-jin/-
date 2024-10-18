<?php include '../login_check.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>月影庵 - 資料リスト</title>
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
        .list-group-item {
            background-color: #f8f9fa; /* ライトグレーの背景 */
            border: 1px solid #dee2e6; /* 枠線の色 */
        }
        .list-group-item:hover {
            background-color: #e9ecef; /* ホバー時の背景色 */
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="container mt-5">
        <h2><i class="fas fa-file-alt"></i> 資料リスト</h2>
        <div class="list-group">
            <a href="./koyoukeiyaku_baito.pdf" class="list-group-item list-group-item-action"><i class="fas fa-file-pdf me-2"></i> 雇用契約書(バイト)</a>
            <a href="./koyoukeiyaku_shain.pdf" class="list-group-item list-group-item-action"><i class="fas fa-file-pdf me-2"></i> 雇用契約書(社員)</a>
            <a href="./seiyakusho.pdf" class="list-group-item list-group-item-action"><i class="fas fa-file-pdf me-2"></i> 誓約書</a>
            <a href="./meibo_kouza.pdf" class="list-group-item list-group-item-action"><i class="fas fa-file-pdf me-2"></i> 名簿/口座</a>
        </div>
    </div>
    <br>

    <?php include "../footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$pdo = null; // データベース接続を閉じる
?>