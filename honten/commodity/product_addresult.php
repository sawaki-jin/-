<?php include '../login_check.php'; ?>
<?php

    // DB に接続
    $dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
    $user = 'root';
    $password = '';
    try {
        // PDOインスタンスの生成
        $db = new PDO($dsn, $user, $password);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        exit('エラー：'.$e->getMessage());
    }

    /* 手続き以外のアクセスを飛ばす */
    if (!isset($_POST['name'])) {
        header('Location: index.php');
        exit();
    }

    // 入力情報をデータベースで変更
    $statement = $db->prepare("INSERT INTO shohin (name, genre, details, price, cost_price) VALUES (?, ?, ?, ?, ?)");
    $statement->execute(array(
        $_POST['name'],
        $_POST['genre'],
        $_POST['details'],
        $_POST['price'],
        $_POST['cost_price']
    ));
     
    header('Location: product_list.php');
    exit();
?>