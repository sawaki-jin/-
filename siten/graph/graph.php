<?php
session_start();

// db接続情報
$dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
$user = 'root';
$password = '';

try {
    // PDOインスタンスの生成
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // 店舗番号を取得
    $stmt = $db->prepare("SELECT tenpo_no FROM employee WHERE ep_no = :id");
    $stmt->bindParam(':id', $_SESSION['ep_no'], PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $tenpo_no = $row ? $row['tenpo_no'] : null;

    $selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
    
    if ($tenpo_no) {
        // 選択された月の売り上げを取得
        $stmt2 = $db->prepare("
            SELECT shohin.name, SUM(uriage.kazu) as total_sales
            FROM uriage
            JOIN shohin ON uriage.shohin_no = shohin.shohin_no
            WHERE uriage.tenpo_no = :tenpo_no AND DATE_FORMAT(uriage.salesdate, '%Y-%m') = :selectedMonth
            GROUP BY shohin.shohin_no, shohin.name
        ");
        $stmt2->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_STR);
        $stmt2->bindParam(':selectedMonth', $selectedMonth, PDO::PARAM_STR);
        $stmt2->execute();
        $sales_data = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sales_data = [];
    }

} catch (PDOException $e) {
    exit('エラー：' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>店舗別商品売り上げ</title>
    <link rel="icon" href="images/favicon.jpg" sizes="any">
    <!-- Bootstrap CSSの読み込み -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/button.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .header-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .header-container h2 {
            margin-right: 20px; /* 店舗別商品売り上げとプルダウンの間のスペース */
        }
    </style>
</head>
<body>
    <?php include('../navbar.php');?>

    <div class="container mt-5">
        <div class="header-container">
            <h2>店舗別商品売り上げ</h2>
            <div class="form-group ml-3" style="display: flex; align-items: center;">
                <label for="monthSelect" class="mr-2">月を選択:</label>
                <select id="monthSelect" name="month" class="form-control" style="width: auto;">
                    <?php
                    for ($m = 0; $m < 12; $m++) {
                        $month = date('Y-m', strtotime("-$m month"));
                        echo '<option value="' . $month . '"' . ($month === $selectedMonth ? ' selected' : '') . '>' . $month . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <canvas id="salesChart" class="mt-4"></canvas>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesData = <?php echo json_encode($sales_data); ?>;
        
        var labels = salesData.map(function(item) {
            return item.name;
        });
        var data = salesData.map(function(item) {
            return item.total_sales;
        });

        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '売り上げ数',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        document.getElementById('monthSelect').addEventListener('change', function() {
            var selectedMonth = this.value;
            window.location.href = '?month=' + selectedMonth;
        });
    });
    </script>
    <?php include "../footer.php";?>
    <!-- Bootstrap JavaScriptの読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
