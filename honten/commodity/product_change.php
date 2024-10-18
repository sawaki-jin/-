<?php include '../login_check.php'; ?>
<?php 
//$_POST['usernum']をローカル変数に格納
$shohin_no=$_POST['shohin_no'];

// データベース接続情報
$dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
$user = 'root';
$password = ''; // パスワードがある場合はここに入力

try { 
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // shohinテーブルからデータを取得するSQLクエリ
    $stmt = $db->prepare("
    SELECT * FROM shohin WHERE shohin_no = :shohin_no");
    // プリペアドステートメントにパラメータを割り当てる
    $stmt->bindParam(":shohin_no", $shohin_no, PDO::PARAM_INT);
    // クエリの実行
    $stmt->execute();
    $row = $stmt->fetch();

} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
    exit();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>経営管理画面</title>
    <style>
        body {
            background: linear-gradient(to right, rgba(192,192,192,1) 0%, rgba(128,128,128,1) 50%);
            font-family: "Roboto", sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .shohin-page {
            width: 100%;
            max-width: 500px;
            padding: 8% 0;
            margin: auto;
        }
        .form {
            position: relative;
            z-index: 1;
            background: #FFFFFF;
            max-width: 90%;
            margin: 0 auto 100px;
            padding: 45px;
            text-align: center;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
            border-radius: 10px;
            overflow: hidden;
            box-sizing: border-box;
        }
        .form h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .form input, .form select ,.form textarea{
            font-family: "Roboto", sans-serif;
            outline: 0;
            background: #f2f2f2;
            width: 100%;
            border: 0;
            margin: 0 0 15px;
            padding: 15px;
            box-sizing: border-box;
            font-size: 14px;
            border-radius: 5px;
        }
        .form textarea{
            resize: none;
            height: 120px;
        }
        .form button {
            font-family: "Roboto", sans-serif;
            text-transform: uppercase;
            outline: 0;
            background: #696969;
            width: 100%;
            border: 0;
            padding: 15px;
            color: #ffffff;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .form button:hover, .form button:active, .form button:focus {
            background: #43A047;
        }
        .form label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }
        .form .text1 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="shohin-page">
    <div class="form">
        <form class="shohin-form" action="product_result.php" method="post">
        <input type="hidden" class="form-control" name="shohin_no" value="<?php echo $row['shohin_no'] ?>">
            <h1>商品情報変更</h1>
            <div class="text1">
                <label for="name">商品名</label>
                <input type="text" id="name" name="name" value="<?php echo $row['name'] ?>" required>
            </div>
            <div class="text1">
                <label for="genre">ジャンル</label>
                <select id="genre" name="genre" required>
                    <option value="" disabled selected>選択してください</option>
                    <option value="肉">1 - 肉</option>
                    <option value="魚">2 - 魚</option>
                    <option value="飯">3 - 飯</option>
                    <option value="飲料">4 - 飲料</option>
                    <option value="前菜">5 - 前菜</option>
                    <option value="デザート">6 - デザート</option>
                    <option value="サービス">7 - サービス</option>
                </select>
            </div>
            <div class="text1">
                <label for="price">価格</label>
                <input type="price" id="price" name="price" value="<?php echo $row['price'] ?>" pattern="^[0-9]+$" maxlength="10" required>
            </div>
            <div class="text1">
                <label for="cost_price">原価</label>
                <input type="cost_price" id="cost_price" name="cost_price" value="<?php echo $row['cost_price'] ?>" pattern="^[0-9]+$" maxlength="10" required>
            </div>
            <div class="textarea">
                <label for="details">詳細</label>
                <textarea type="details" id="details" name="details" required><?php echo $row['details'] ?></textarea>
                
            </div>
            <button class="button" type="submit">変更</button>
        </form>
    </div>
</div>
</body>
</html>
