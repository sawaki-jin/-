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

// 指定された商品の月別売上データを取得
$shohin_no = isset($_GET['shohin_no']) ? $_GET['shohin_no'] : '';
$sql = "
SELECT 
    DATE_FORMAT(u.salesdate, '%Y-%m') AS sale_month,
    SUM(u.kazu) AS total_sales
FROM URIAGE u
WHERE u.shohin_no = :shohin_no
GROUP BY sale_month
ORDER BY sale_month
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':shohin_no', $shohin_no, PDO::PARAM_STR);
$stmt->execute();
$productSalesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON形式で出力
header('Content-Type: application/json');
echo json_encode($productSalesData);
?>
