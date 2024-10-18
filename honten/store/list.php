<?php 
    session_start();
    
    //DBに接続
    $dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
    $user = 'root';
    $password = '';
  
    try {
        $db = new PDO($dsn, $user, $password);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $stmt1 = $db->prepare("
            SELECT * FROM tenpo
        ");
        

        $stmt1->execute();
    } catch (PDOException $e) {
        exit('エラー：'.$e->getMessage());
    }
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>店舗一覧</title>
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
        .action-buttons {
            display: flex;
            justify-content: space-evenly;
        }
        .action-buttons form {
            display: inline;
        }
        /* 検索窓の幅調整 */
        #searchInput {
            width: 200px; 
            margin-right: 10px; 
        }
    </style>
</head>
<body>
    <?php include('../navbar.php'); ?>

    <div class="container mt-5" style="max-width: 700px;">
        <h2><i class="fas fa-store-alt"></i> 店舗一覧</h2>
        <div class="d-flex align-items-center mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="キーワードで検索">
        </div>
        <div class="table-responsive">
            <table class="table table-hover" id="listTable">
                <thead>
                    <tr>
                        <th>店舗番号</th>
                        <th>店舗名</th>
                        <th>エリア</th>
                        <th>評価</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = $stmt1->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['tenpo_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['tenpo_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['erea_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <form action="hyouka.php" method="post" class="d-inline">
                                <input type="hidden" name="tenpo_no" value="<?php echo $row['tenpo_no'] ?>">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-star"></i> 評価</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <br>

    <?php include("../footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            let tableRows = document.getElementById('listTable').getElementsByTagName('tr');
            
            for (let i = 1; i < tableRows.length; i++) {
                let rowText = tableRows[i].textContent.toLowerCase();
                if (rowText.indexOf(searchValue) > -1) {
                    tableRows[i].style.display = '';
                } else {
                    tableRows[i].style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>