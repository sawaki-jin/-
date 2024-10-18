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
            border-right: 1px solid #dee2e6;
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
        .narrow-col {
            width: 80px;
        }
    </style>
</head>
<body>
    <?php
    include('../navbar.php');

    $dsn = "mysql:dbname=bbadb;host=localhost;charset=utf8mb4";
    $user = 'root';
    $pass = '';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = null;
    $contacts = [];
    $contact_type = null;

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contact_no'])) {
            $contact_no = $_POST['contact_no'];
            $updateSql = "UPDATE contact SET status = 1 WHERE contact_no = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$contact_no]);
        }

        $contact_type = isset($_GET['contact_type']) ? $_GET['contact_type'] : null;

        $sql = "SELECT * FROM contact con
                INNER JOIN tenpo ten ON con.contactmoto_no = ten.tenpo_no
                WHERE contactsaki_no = 1 AND status = 0";
        
        if ($contact_type) {
            $sql .= " AND contact_type = :contact_type";
        }

        $stmt = $pdo->prepare($sql);
        if ($contact_type) {
            $stmt->bindParam(':contact_type', $contact_type);
        }

        $stmt->execute();

        while ($row = $stmt->fetch()) {
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
                    <option value="申請" <?php if ($contact_type == '申請') echo 'selected'; ?>>申請</option>
                    <option value="報告" <?php if ($contact_type == '報告') echo 'selected'; ?>>報告</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> 絞り込み</button>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 10%">連絡番号</th>
                        <th style="width: 15%">連絡種類</th>
                        <th style="width: 25%">連絡店舗</th>
                        <th style="width: 40%">連絡内容</th>
                        <th style="width: 10%">受付</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (empty($contacts)) {
                            echo "<tr><td colspan='5'>contactsaki_no = 1 のデータが見つかりません。";
                            echo " デバッグ情報: contactsaki_no = 1 のレコードが存在するか、contactmoto_no と tenpo_no の対応が正しいか確認してください。</td></tr>";
                        } else {
                            foreach ($contacts as $contact): 
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($contact['contact_no']); ?></td>
                            <td><?php echo htmlspecialchars($contact['contact_type']); ?></td>
                            <td><?php echo htmlspecialchars($contact['tenpo_name']); ?></td>
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
    $pdo = null;
    include("../footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>