<?php
session_start();

//DBに接続
$dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
$user = 'root';
$password = '';

if(isset($_POST['tenpo_name'])){
    $tenpo_name = $_POST['tenpo_name'];
    $erea_name = $_POST['erea_name'];

    try {
        $db = new PDO($dsn, $user, $password);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        //店舗追加
        $stmt = $db->prepare("
            INSERT INTO tenpo (tenpo_no, tenpo_name, erea_name)
            VALUES (0, :tenpo_name, :erea_name)
        ");
        
        $stmt->bindParam(':tenpo_name', $tenpo_name, PDO::PARAM_STR);
        $stmt->bindParam(':erea_name', $erea_name, PDO::PARAM_STR);

        $stmt->execute();

        //店舗Noの取得
        $stmt0 = $db->prepare("
            SELECT MAX(tenpo_no) AS tenpo_no FROM tenpo; 
        ");

        $stmt0->execute();

        while($row = $stmt0->fetch()){
            $tenpo_no = $row['tenpo_no'];
        }

        $stmt1 = $db->prepare("
            SELECT * FROM shohin
        ");

        $stmt1->execute();
        
        while($row = $stmt1->fetch()){
            //在庫作成
            $stmt = $db->prepare("
            INSERT INTO stock_control (shohin_no, tenpo_no, stock)
            VALUES (:shohin_no, :tenpo_no, 0)
            ");

            $stmt->bindParam(':shohin_no', $row['shohin_no'], PDO::PARAM_INT);
            $stmt->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_INT);

            $stmt->execute();
        }

        header('Location: list.php');
        exit();

    }catch (PDOException $e) {
        exit('エラー：'.$e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗追加</title>
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
        .tenpo-page {
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
        .form input {
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
<div class="tenpo-page">
    <div class="form">
        <form class="tenpo-form" action="add.php" method="post">
            <h1>店舗追加</h1>
            <div class="text1">
                <label for="tenpo_name">店舗名</label>
                <input type="text" id="tenpo_name" name="tenpo_name" required>
            </div>
            <div class="text1">
                <label for="erea_name">地域名</label>
                <input type="text" id="erea_name" name="erea_name" required>
            </div>
            <button type="submit" onclick="return Check()">送信</button>
        </form>
    </div>
</div>
</body>
</html>