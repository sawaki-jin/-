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

// 指定された店舗と月の商品の売上データを取得
$tenpo_no = isset($_GET['tenpo_no']) ? $_GET['tenpo_no'] : '';
$sale_month = isset($_GET['sale_month']) ? $_GET['sale_month'] : '';
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
$stmt->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_STR);
$stmt->bindParam(':sale_month', $sale_month, PDO::PARAM_STR);
$stmt->execute();
$salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON形式で出力
header('Content-Type: application/json');
echo json_encode($salesData);
?>
