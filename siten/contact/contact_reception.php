<?php include '../login_check.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>月影庵 - 連絡リスト</title>
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
        #contact_type {
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
        .narrow-col {
            width: 100px; /* 受付列の幅を指定 */
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

    $pdo = null;
    $contacts = [];
    $contact_type = null;

    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ボタンが押された場合の処理
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contact_no'])) {
            $contact_no = $_POST['contact_no'];
            $updateSql = "UPDATE contact SET status = 1 WHERE contact_no = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$contact_no]);
        }

        // フィルター条件の取得
        $contact_type = isset($_GET['contact_type']) ? $_GET['contact_type'] : null;

        // contactテーブルからデータを取得するSQLクエリ
        $sql = "SELECT contact_no, contact_type, contact_content FROM contact WHERE status = 0 AND contactsaki_no = :contactsaki_no";
        
        if ($contact_type) {
            $sql .= " AND contact_type = :contact_type";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':contactsaki_no', $_SESSION['ep_no']);
        
        if ($contact_type) {
            $stmt->bindParam(':contact_type', $contact_type);
        }

        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contacts[] = $row;
        }
        
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
        exit();
    }
    ?>

    <div class="container mt-5">
        <h2><i class="fas fa-envelope"></i> 連絡リスト</h2>
        <form method="GET" action="" class="d-flex align-items-center mb-3">
            <div class="form-group me-3"> 
                <select id="contact_type" name="contact_type" class="form-select">
                    <option value="">全て</option>
                    <option value="指示" <?php if ($contact_type == '指示') echo 'selected'; ?>>指示</option>
                    <option value="連絡" <?php if ($contact_type == '連絡') echo 'selected'; ?>>連絡</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> 絞り込み</button>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 15%">連絡番号</th>
                        <th style="width: 20%">連絡種類</th>
                        <th style="width: 55%">連絡内容</th>
                        <th style="width: 10%">受付</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (empty($contacts)) {
                            echo "<tr><td colspan='4'>データが見つかりません。";
                            // デバッグ情報を出力
                            echo " デバッグ情報: contactsaki_no = ".$_SESSION['ep_no']." のレコードが存在するか、contactmoto_no と tenpo_no の対応が正しいか確認してください。</td></tr>";
                        } else {
                            foreach ($contacts as $contact): 
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($contact['contact_no']); ?></td>
                            <td><?php echo htmlspecialchars($contact['contact_type']); ?></td>
                            <td><?php echo htmlspecialchars($contact['contact_content']); ?></td>
                            <td class="narrow-col">
                                <form method='POST' action=''>
                                    <input type='hidden' name='contact_no' value='<?php echo $contact['contact_no']; ?>'>
                                    <button type='submit' class='btn btn-success'><i class="fas fa-check"></i> 受付</button>
                                </form>
                            </td>
                        </tr>
                    <?php 
                            endforeach; 
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