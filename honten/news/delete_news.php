<?php
include '../login_check.php';

// データベース接続情報
try {
    $dsn = "mysql:dbname=bbadb;host=localhost;charset=utf8mb4";
    $username = "root";
    $password = ""; // パスワードが必要であればここに設定
    $options = [];
    $pdo = new PDO($dsn, $username, $password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $msg = 'データベース接続に失敗しました: ' . $e->getMessage();
    echo "<div class='login-page'><div class='form'><h1>$msg</h1></div></div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;

    if ($article_id > 0) {
        // ニュース記事を削除
        $sql = "DELETE FROM news_articles WHERE article_id = :article_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // 削除後のリダイレクト
            header("Location: news_list.php");
            exit;
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "エラー: " . $errorInfo[2];
        }
    } else {
        echo "無効な記事IDです。";
    }
} else {
    echo "不正なリクエストです。";
    exit;
}
?>
