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
    if (!isset($_POST['shohin_no'])) {
        header('Location: index.php');
        exit();
    }

    // 入力情報をデータベースで変更
    $statement = $db->prepare("UPDATE shohin SET name=?, details=?,genre=?, price=?, cost_price=? WHERE shohin_no=?");
    $statement->execute(array(
        $_POST['name'],
        $_POST['details'],
        $_POST['genre'],
        $_POST['price'],
        $_POST['cost_price'],
        $_POST['shohin_no']
     ) );
     
    header('Location: product_list.php');
    exit();
?>