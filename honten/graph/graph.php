<?php include '../login_check.php'; ?>
<?php
// データベース接続情報
$host = 'localhost';
$dbname = 'bbadb';
$user = 'root';
$pass = '';

// データベース接続
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続失敗: " . $e->getMessage());
}

// "本社"を除く店舗リストを取得
$tenpoSql = "SELECT tenpo_no, tenpo_name FROM TENPO WHERE tenpo_name != '本社'";
$tenpoStmt = $pdo->prepare($tenpoSql);
$tenpoStmt->execute();
$tenpoList = $tenpoStmt->fetchAll(PDO::FETCH_ASSOC);

// 最初の店舗の利用可能な月を取得
$initialTenpoNo = $tenpoList[0]['tenpo_no'];
$monthSql = "
SELECT DISTINCT DATE_FORMAT(salesdate, '%Y-%m') AS sale_month
FROM URIAGE
WHERE tenpo_no = :tenpo_no
ORDER BY sale_month
";
$monthStmt = $pdo->prepare($monthSql);
$monthStmt->bindParam(':tenpo_no', $initialTenpoNo, PDO::PARAM_STR);
$monthStmt->execute();
$monthList = $monthStmt->fetchAll(PDO::FETCH_ASSOC);

// 商品リストを取得
$productSql = "SELECT shohin_no, name FROM SHOHIN";
$productStmt = $pdo->prepare($productSql);
$productStmt->execute();
$productList = $productStmt->fetchAll(PDO::FETCH_ASSOC);

// 初期の店舗と月の売上データを取得
$initialMonth = $monthList[0]['sale_month'];
$sql = "
SELECT 
    sh.name AS shohin_name,
    SUM(u.kazu) AS total_sales
FROM URIAGE u
JOIN SHOHIN sh ON u.shohin_no = sh.shohin_no
WHERE u.tenpo_no = :tenpo_no AND DATE_FORMAT(u.salesdate, '%Y-%m') = :sale_month
GROUP BY sh.name
ORDER BY sh.name
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':tenpo_no', $initialTenpoNo, PDO::PARAM_STR);
$stmt->bindParam(':sale_month', $initialMonth, PDO::PARAM_STR);
$stmt->execute();
$salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 初期の商品の月別売上データを取得
$initialProductNo = $productList[0]['shohin_no'];
$productSalesSql = "
SELECT 
    DATE_FORMAT(u.salesdate, '%Y-%m') AS sale_month,
    SUM(u.kazu) AS total_sales
FROM URIAGE u
WHERE u.shohin_no = :shohin_no
GROUP BY sale_month
ORDER BY sale_month
";
$productSalesStmt = $pdo->prepare($productSalesSql);
$productSalesStmt->bindParam(':shohin_no', $initialProductNo, PDO::PARAM_STR);
$productSalesStmt->execute();
$productSalesData = $productSalesStmt->fetchAll(PDO::FETCH_ASSOC);

// HTML出力の開始
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>店舗別商品売上グラフ</title>
    <link rel="stylesheet" href="graph.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="content">
        <div class="selectors">
            <select id="tenpoSelect">
                <?php foreach ($tenpoList as $tenpo): ?>
                    <option value="<?php echo htmlspecialchars($tenpo['tenpo_no'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($tenpo['tenpo_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select id="monthSelect">
                <?php foreach ($monthList as $month): ?>
                    <option value="<?php echo htmlspecialchars($month['sale_month'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($month['sale_month'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <canvas id="salesChart"></canvas>
        
        <div class="selectors">
            <select id="productSelect">
                <?php foreach ($productList as $product): ?>
                    <option value="<?php echo htmlspecialchars($product['shohin_no'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <canvas id="productSalesChart"></canvas>
    </div>
    <script>
        // グローバル変数
        let chart, productChart;

        // グラフを描画する関数
        function drawChart(data, chartElementId, chartVar) {
            const labels = data.map(item => item.shohin_name || item.sale_month);
            const sales = data.map(item => item.total_sales);

            const ctx = document.getElementById(chartElementId).getContext('2d');
            if (chartVar) {
                chartVar.destroy(); // 既存のグラフを破棄
            }
            chartVar = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '売上数',
                        data: sales,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: '商品名' // デフォルトのラベル
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: '売上数'
                            }
                        }
                    }
                }
            });
            return chartVar;
        }

        // 初期データでグラフを描画
        chart = drawChart(<?php echo json_encode($salesData); ?>, 'salesChart', chart);
        productChart = drawChart(<?php echo json_encode($productSalesData); ?>, 'productSalesChart', productChart);

        // 店舗選択時に月のリストを更新
        document.getElementById('tenpoSelect').addEventListener('change', function() {
            const tenpoNo = this.value;
            fetch(`fetch_months.php?tenpo_no=${tenpoNo}`)
                .then(response => response.json())
                .then(data => {
                    const monthSelect = document.getElementById('monthSelect');
                    monthSelect.innerHTML = '';
                    data.forEach(month => {
                        const option = document.createElement('option');
                        option.value = month.sale_month;
                        option.textContent = month.sale_month;
                        monthSelect.appendChild(option);
                    });
                    // 新しい月リストの最初の月を選択して売上データを更新
                    fetchSalesData(tenpoNo, data[0].sale_month);
                });
        });

        // 月選択時に売上データを更新
        document.getElementById('monthSelect').addEventListener('change', function() {
            const tenpoNo = document.getElementById('tenpoSelect').value;
            const saleMonth = this.value;
            fetchSalesData(tenpoNo, saleMonth);
        });

        // 商品選択時に月別売上データを更新
        document.getElementById('productSelect').addEventListener('change', function() {
            const productNo = this.value;
            fetchProductSalesData(productNo);
        });

        // 売上データを取得してグラフを描画する関数
        function fetchSalesData(tenpoNo, saleMonth) {
            fetch(`fetch_sales_data.php?tenpo_no=${tenpoNo}&sale_month=${saleMonth}`)
                .then(response => response.json())
                .then(data => {
                    chart = drawChart(data, 'salesChart', chart);
                });
        }

        // 商品の月別売上データを取得してグラフを描画する関数
        function fetchProductSalesData(productNo) {
            fetch(`fetch_product_sales_data.php?shohin_no=${productNo}`)
                .then(response => response.json())
                .then(data => {
                    productChart = drawChart(data, 'productSalesChart', productChart);
                });
        }
    </script>
    <?php include "../footer.php";?>
</body>
</html>
