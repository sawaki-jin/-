<?php include '../login_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ただいまログイン中</title>
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
        .login-page {
            width: 100%;
            max-width: 360px;
            padding: 8% 0;
            margin: auto;
        }
        .form {
            position: relative;
            z-index: 1;
            background: #FFFFFF;
            padding: 45px;
            text-align: center;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
            border-radius: 10px;
        }
        .form h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

    </style>
</head>
<body>

<?php
//フォームからの値をそれぞれ変数に代入
$tenpo_no = $_POST['tenpo_no'];
$ep_name = $_POST['ep_name'];
$ep_no = $_POST['ep_no'];
$yakusyoku = $_POST['yakusyoku'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// $dsn = "mysql:dbname=bbadb;host=localhost;charset=utf8";
// $username = "xxx";
// $password = "";
try {
    $dsn = "mysql:dbname=bbadb;host=localhost;charset=utf8mb4";
    $username = "root";
    $options = [];
    $pdo = new PDO($dsn, $username);
    // $dbh = new PDO('mysql:host=localhost;dbname=bbadb', 'ep_no', 'password');

} catch (PDOException $e) {
    $msg = $e->getMessage();
}

//フォームに入力されたmailがすでに登録されていないかチェック
$sql = "SELECT * FROM employee WHERE ep_no = :ep_no";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':ep_no', $ep_no);
$stmt->execute();
$member = $stmt->fetch(PDO::FETCH_ASSOC);
if (is_array($member) && isset($member['ep_no']) && $member['ep_no'] == $ep_no) {
    $msg = '<p>同じ従業員番号が</p><p>存在します。</p>';
    //header("Refresh: 2; URL=http://localhost/seminar2/kadai04/CT3B/honten/human_affairs/new_login.php");
    $link = '<a href="new_login.php">戻る</a>';
} else {
    //登録されていなければinsert 
    $sql = "INSERT INTO employee(ep_no,ep_name,tenpo_no,yakusyoku,password) VALUES (:ep_no,:ep_name,:tenpo_no,:yakusyoku,:password)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':tenpo_no', $tenpo_no);
    $stmt->bindValue(':ep_name', $ep_name);
    $stmt->bindValue(':ep_no', $ep_no);
    $stmt->bindValue(':yakusyoku',$yakusyoku);
    $stmt->bindValue(':password', $password);
    $stmt->execute();
    $msg = '<p>従業員登録が</p><p>完了しました。</p>';
    //header("Refresh: 2; URL=http://localhost/seminar2/kadai04/CT3B/honten/login.php");
    $link = '<a href="../login.php">ログインページ</a>';
}
?>
<div class="login-page">
    <div class="form">
        <form class="login-form" action="login.php" method="post">
        <h1><?php echo $msg; ?></h1><!--メッセージの出力-->
        <?php echo $link; ?>
        </form>
    </div>
</div>
</body>
</html>
