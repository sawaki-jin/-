<?php include '../login_check.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>ニュース記事登録フォーム</title>
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
        .preview {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>
    <div class="container">
    <?php
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

    // フォームから送信されたデータを取得
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $author = $_POST['author'];
        $store = $_POST['store'];

        // 画像の処理
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = file_get_contents($_FILES['image']['tmp_name']);
        } else {
            $image = null;
        }

        // SQLクエリを準備して実行
        $sql = "INSERT INTO news_articles (title, content, author, image, store) VALUES (:title, :content, :author, :image, :store)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':content', $content);
        $stmt->bindValue(':author', $author);
        $stmt->bindValue(':image', $image, PDO::PARAM_LOB);
        $stmt->bindValue(':store', $store);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success' role='alert'>ニュース記事が登録されました</div>";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "<div class='alert alert-danger' role='alert'>エラー: " . $errorInfo[2] . "</div>";
        }
    }

    // 店舗名の取得
    $sql = "SELECT tenpo_no, tenpo_name FROM tenpo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 店舗と著者の対応リストの取得
    $sql = "SELECT tenpo.tenpo_name, employee.ep_name 
            FROM employee 
            JOIN tenpo ON employee.tenpo_no = tenpo.tenpo_no";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

        <h1 class="text-center">ニュース記事登録フォーム</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">ニュースタイトル:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">記事内容:</label>
                <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
            </div>
            <div class="form-group">
                <input type="hidden" class="form-control" id="author" name="author" readonly required>
            </div>
            <div class="form-group">
                <label for="store">店舗名:</label>
                <select class="form-control" id="store" name="store" required onchange="updateAuthor()">
                    <option value="">店舗を選択してください</option>
                    <?php foreach ($stores as $store): ?>
                        <option value="<?php echo htmlspecialchars($store['tenpo_name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($store['tenpo_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="image">画像:</label>
                <input type="file" class="form-control-file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                <img id="preview" class="preview">
            </div>
            <button type="submit" class="btn btn-primary">登録</button>
        </form>
    </div>

    <script>
        const authors = <?php echo json_encode($authors); ?>;

        function updateAuthor() {
            const storeSelect = document.getElementById('store');
            const authorInput = document.getElementById('author');
            const selectedStore = storeSelect.value;

            const author = authors.find(a => a.tenpo_name === selectedStore);
            authorInput.value = author ? author.ep_name : '';
        }

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
