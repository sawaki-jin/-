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

// 指定された店舗の利用可能な月を取得
$tenpo_no = isset($_GET['tenpo_no']) ? $_GET['tenpo_no'] : '';
$sql = "
SELECT DISTINCT DATE_FORMAT(salesdate, '%Y-%m') AS sale_month
FROM URIAGE
WHERE tenpo_no = :tenpo_no
ORDER BY sale_month
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_STR);
$stmt->execute();
$monthList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON形式で出力
header('Content-Type: application/json');
echo json_encode($monthList);
?>
