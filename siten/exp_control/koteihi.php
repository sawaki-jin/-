<?php 
    session_start();

    //DBに接続
    $dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
    $user = 'root';
    $password = '';

    try {
        $db = new PDO($dsn, $user, $password);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        // 店舗番号を取得
        $tenpo_no = $_SESSION['ep_no'];

        // 最小の年度を取得
        $sql = "SELECT MIN(salesdate) AS mindate FROM exp_control WHERE tenpo_no = :tenpo_no";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_INT);
        $stmt->execute();
        $minyear = substr($stmt->fetchColumn(), 0, 4);

        // 最大の年度を取得
        $sql = "SELECT MAX(salesdate) AS maxdate FROM exp_control WHERE tenpo_no = :tenpo_no";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_INT);
        $stmt->execute();
        $maxyear = substr($stmt->fetchColumn(), 0, 4);

        // 年度ごとのデータを初期化
        $data = [];
        for ($yr = $minyear; $yr <= $maxyear; $yr++) {
            $data[$yr] = [
                'chidaiyatin' => array_fill(1, 12, 0),
                'genkashokyakuhi' => array_fill(1, 12, 0),
                'siharairisoku' => array_fill(1, 12, 0),
                'risuryou' => array_fill(1, 12, 0),
                'tuusinhi' => array_fill(1, 12, 0),
                'hokenryou' => array_fill(1, 12, 0),
                'koteihisonota' => array_fill(1, 12, 0),
                'total' => array_fill(1, 12, 0),
            ];
        }

        // データベースからデータを取得
        $sql = "SELECT * FROM exp_control WHERE tenpo_no = :tenpo_no";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_INT);
        $stmt->execute();

        // データを配列に格納
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $year = substr($row['salesdate'], 0, 4);
            $month = intval(substr($row['salesdate'], 5, 2));

            $data[$year]['chidaiyatin'][$month] += $row['chidaiyatin'];
            $data[$year]['genkashokyakuhi'][$month] += $row['genkashokyakuhi'];
            $data[$year]['siharairisoku'][$month] += $row['siharairisoku'];
            $data[$year]['risuryou'][$month] += $row['risuryou'];
            $data[$year]['tuusinhi'][$month] += $row['tuusinhi'];
            $data[$year]['hokenryou'][$month] += $row['hokenryou'];
            $data[$year]['koteihisonota'][$month] += $row['koteihisonota'];

            // 合計を計算
            $data[$year]['total'][$month] = $data[$year]['chidaiyatin'][$month] + 
                                            $data[$year]['genkashokyakuhi'][$month] +
                                            $data[$year]['siharairisoku'][$month] +
                                            $data[$year]['risuryou'][$month] +
                                            $data[$year]['tuusinhi'][$month] +
                                            $data[$year]['hokenryou'][$month] +
                                            $data[$year]['koteihisonota'][$month];
        }

    } catch (PDOException $e) {
        die('エラー: ' . $e->getMessage());
    }
    ?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>固定費</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/test.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
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
            display: flex; /* ロゴとタイトルを横並びにする */
            align-items: center; /* 垂直方向に中央揃え */
        }
        h2 i {
            margin-right: 10px; /* ロゴとタイトルの間隔 */
            font-size: 1.5em; /* ロゴのサイズ */
        }
        .btn-primary {
            background-color: #007bff;
        }
        .table {
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            width: 100%;
            /* table-layout: fixed; */ /* table-layout: fixed を削除 */ 
        }
        .table thead {
            background-color: #343a40;
            color: white;
        }
        .table td {
            vertical-align: middle;
            border-right: 1px solid #dee2e6; 
            overflow: hidden; 
            /* text-overflow: ellipsis;  省略記号を削除 */
            white-space: nowrap; 
        }
        /* 合計行のスタイル */
        .table tbody tr:last-child td { 
            font-weight: bold; /* 太字 */
            border-top: 2px solid #dee2e6; /* 線を太く */
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
        .form-inline select {
            margin-right: 10px;
            max-width: 200px;
        }
        /* 左側の項目名の幅を広げる */
        .table td:first-child {
            width: 15%; /* 例: 15%幅 */
        }
    </style>
</head>
<body>
    <?php include('../navbar.php') ?>

    <div class="container mt-5">
        <h2><i class="fas fa-file-invoice-dollar"></i> 固定費</h2> 

        <div class="search-newlogin-area">
            <form class="form-inline">
                <label for="yearSelect">年度を選択：</label>
                <select id="yearSelect" class="form-select">
                    <?php for ($yr = $minyear; $yr <= $maxyear; $yr++) : ?>
                        <option value="<?= $yr ?>" <?= $yr == $maxyear ? 'selected' : '' ?>><?= $yr ?>年</option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>

        <div id="tables">
            <?php for ($yr = $minyear; $yr <= $maxyear; $yr++) : ?>
                <div class="table-container" id="table-<?= $yr ?>" style="display: <?= $yr == $maxyear ? 'block' : 'none' ?>;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th>1月</th>
                                <th>2月</th>
                                <th>3月</th>
                                <th>4月</th>
                                <th>5月</th>
                                <th>6月</th>
                                <th>7月</th>
                                <th>8月</th>
                                <th>9月</th>
                                <th>10月</th>
                                <th>11月</th>
                                <th>12月</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data[$yr] as $key => $values) : ?>
                                <?php if ($key !== 'total') : ?>
                                <tr>
                                    <td><?= str_replace(['chidaiyatin', 'genkashokyakuhi', 'siharairisoku', 'risuryou', 'tuusinhi', 'hokenryou', 'koteihisonota'], 
                                                       ['賃貸家賃', '減価償却費', '支払利息', 'リース料', '通信費', '保険料', 'その他'], $key) ?></td>
                                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                                        <td><?= $values[$i] ?></td>
                                    <?php endfor; ?>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <tr> 
                                <td>合計</td>
                                <?php for ($i = 1; $i <= 12; $i++) : ?>
                                    <td><?= $data[$yr]['total'][$i] ?></td> 
                                <?php endfor; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <br>

    <script>
        document.getElementById('yearSelect').addEventListener('change', function() {
            var selectedYear = this.value;
            var tables = document.querySelectorAll('.table-container');
            tables.forEach(function(table) {
                table.style.display = table.id === 'table-' + selectedYear ? 'block' : 'none';
            });
        });
    </script>

    <?php
    include("../footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>