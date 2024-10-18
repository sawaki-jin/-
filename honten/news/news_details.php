<?php include '../login_check.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>ニュース記事</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 100px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        body h1 {
            padding-bottom: 10px;
        }
        p {
            line-height: 1.6;
        }
        .author {
            margin-top: 20px;
            font-style: italic;
            color: #666;
        }
        .store {
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }
        .image {
            margin-top: 20px;
        }
        .image img {
            max-width: 100%;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>
    <?php
    if (!isset($_GET['article_id'])) {
        echo "<p>IDが指定されていません。</p>";
        exit;
    }

    $article_id = intval($_GET['article_id']);

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

    // ニュース記事を取得
    $sql = "SELECT title, content, author, image, store FROM news_articles WHERE article_id = :article_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->execute();
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($article) {
        $title = htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8');
        $author = htmlspecialchars($article['author'], ENT_QUOTES, 'UTF-8');
        $store = htmlspecialchars($article['store'], ENT_QUOTES, 'UTF-8');
        $image = $article['image'] ? base64_encode($article['image']) : null;

        echo "<div class='container'>";
        echo "<h1>$title</h1>";
        echo "<p class='store'>対象： $store</p>";

        if ($image) {
            echo "<div class='image'><img src='data:image/jpeg;base64,$image' alt='ニュース画像'></div>";
        }

    } else {
        echo "<p>記事が見つかりません。</p>";
    }
        echo "<p>$content</p>";
        echo "<p class='author'>著者: $author</p>";
        echo "</div>";
    ?>
</body>

<footer>
<?php include '../footer.php'; ?>
  </footer>
</html>
