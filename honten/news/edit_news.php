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

// 編集対象のニュース記事IDを取得
$article_id = isset($_GET['article_id']) ? intval($_GET['article_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームから送信されたデータを取得
    $title = $_POST['title'];
    $store = $_POST['store'];
    $content = $_POST['content'];
    $author = $_POST['author'];
    $image = null;

    // 画像のアップロード処理
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    // データベースを更新
    $sql = "UPDATE news_articles SET title = :title, store = :store, content = :content, author = :author";
    if ($image !== null) {
        $sql .= ", image = :image";
    }
    $sql .= " WHERE article_id = :article_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':store', $store);
    $stmt->bindValue(':content', $content);
    $stmt->bindValue(':author', $author);
    if ($image !== null) {
        $stmt->bindValue(':image', $image, PDO::PARAM_LOB);
    }
    $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "ニュース記事が更新されました";
        // 更新後のリダイレクト
        header("Location: news_list.php");
        exit;
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "エラー: " . $errorInfo[2];
    }
} else {
    // 記事のデータを取得
    $sql = "SELECT title, store, content, author, image FROM news_articles WHERE article_id = :article_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->execute();
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        echo "指定された記事が見つかりません。";
        exit;
    }
}

$sql = "SELECT tenpo_name FROM tenpo";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>ニュース記事の編集</title>
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
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
        }
        input[type="text"], textarea, input[type="date"], select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        button {
            margin-top: 20px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .preview {
            max-width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>
    <div class="container">
        <h1>ニュース記事の編集</h1>
        <form method="post" enctype="multipart/form-data">
            <label for="title">タイトル</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="store">店舗</label>
            <select id="store" name="store" required>
                <?php foreach ($stores as $store): ?>
                    <option value="<?php echo htmlspecialchars($store['tenpo_name'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($store['tenpo_name'] === $article['store']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($store['tenpo_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="content">内容</label>
            <textarea id="content" name="content" required><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>

            <label for="author">著者</label>
            <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($article['author'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="image">画像:</label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
            <?php if ($article['image']): ?>
                <img id="preview" class="preview" src="data:image/jpeg;base64,<?php echo base64_encode($article['image']); ?>" alt="現在の画像">
            <?php else: ?>
                <img id="preview" class="preview" alt="プレビュー">
            <?php endif; ?>

            <button type="submit">更新</button>
        </form>
    </div>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
<footer>
<?php include '../footer.php'; ?>
  </footer>
</html>
