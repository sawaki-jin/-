<?php
session_start();

//DBに接続
$dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
$user = 'root';
$password = '';

$db = new PDO($dsn, $user, $password);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(isset($_POST['kazu'])){
  //データ受け取り
  $shohin_nos = $_POST['shohin_no'];
  $kazus = $_POST['kazu'];

  $exp_uriage = 0;
  $check = 0;

  try {
    $db->beginTransaction(); // トランザクション開始

    // 当日の売上があるかチェック
    $stmt5 = $db->prepare(" 	
        SELECT 1 FROM uriage
        WHERE salesdate = CURRENT_DATE
        AND tenpo_no = :tenpo_no
        LIMIT 1
    ");
    $stmt5->bindParam(':tenpo_no', $_SESSION['ep_no'], PDO::PARAM_INT);
    $stmt5->execute();

    if ($stmt5->fetchColumn()) {
      //当日の売上入力があった場合
      for($i = 0; $i < count($shohin_nos); $i++){
        //uriage db を更新
        $stmt1 = $db->prepare("
          UPDATE uriage
          SET kazu = :kazu
          WHERE salesdate = CURRENT_DATE
          AND tenpo_no = :tenpo_no
          AND shohin_no = :shohin_no
        ");
        $stmt1->bindParam(':kazu', $kazus[$i], PDO::PARAM_INT);
        $stmt1->bindParam(':tenpo_no', $_SESSION['ep_no'], PDO::PARAM_INT);
        $stmt1->bindParam(':shohin_no', $shohin_nos[$i], PDO::PARAM_INT);
        $stmt1->execute();
      }   
    } else {
      //当日の売上入力がなかった場合
      for($i = 0; $i < count($shohin_nos); $i++){
        //uriage db に追加
        $stmt1 = $db->prepare("
          INSERT INTO uriage (shohin_no, kazu, salesdate, tenpo_no)
          VALUES (:shohin_no, :kazu, CURRENT_DATE, :tenpo_no)
        ");
        $stmt1->bindParam(':shohin_no', $shohin_nos[$i], PDO::PARAM_INT);
        $stmt1->bindParam(':kazu', $kazus[$i], PDO::PARAM_INT);
        $stmt1->bindParam(':tenpo_no', $_SESSION['ep_no'], PDO::PARAM_INT);
        $stmt1->execute();
      }
    }

    // exp_uriage db のuriageの値取得
    $stmt2 =$db->prepare("
        SELECT uriage.kazu, shohin.price
        FROM uriage
        LEFT JOIN shohin
        ON shohin.shohin_no = uriage.shohin_no
        WHERE tenpo_no = :tenpo_no
        AND salesdate = CURRENT_DATE
    ");
    $stmt2->bindParam(':tenpo_no', $_SESSION['ep_no'], PDO::PARAM_INT);
    $stmt2->execute();

    while($row = $stmt2->fetch()){
      $exp_uriage += $row['kazu'] * $row['price'];
    }

    // DB exp_controlにデータがあるか確認
    $stmt3 = $db->prepare("
        SELECT 1 FROM exp_control
        WHERE tenpo_no = :tenpo_no
        AND salesdate = CURRENT_DATE
        LIMIT 1
    ");
    $stmt3->bindParam(':tenpo_no', $_SESSION['ep_no'], PDO::PARAM_INT);
    $stmt3->execute();

    if ($stmt3->fetchColumn()) {
      // DB exp_controlにデータがある場合update
      $stmt4 =$db->prepare("
          UPDATE exp_control
          SET uriage = :uriage
          WHERE tenpo_no = :tenpo_no
          AND salesdate = CURRENT_DATE;
      ");
      $stmt4->bindParam(':uriage', $exp_uriage, PDO::PARAM_INT);
      $stmt4->bindParam(':tenpo_no', $_SESSION['ep_no'], PDO::PARAM_INT);
      $stmt4->execute();
    } else {
      // DB exp_uriageにデータがない場合insert
      $stmt4 =$db->prepare("
          INSERT INTO exp_control 
            (tenpo_no, salesdate, uriage,
            chidaiyatin,genkashokyakuhi,siharairisoku,risuryou,tuusinhi, hokenryou,koteihisonota,
            shokuzaihi,jinkenhi,suidoukounetuhi,hanbaisokusinhi,housouhi,hendouhisonota
          )
          VALUES (:tenpo_no, CURRENT_DATE, :uriage, 
                  0,0,0,0,0,0,0,
                  0,0,0,0,0,0
          )
      ");
      $stmt4->bindParam(':tenpo_no', $_SESSION['ep_no'], PDO::PARAM_INT);
      $stmt4->bindParam(':uriage', $exp_uriage, PDO::PARAM_INT);
      $stmt4->execute();
    }

    $db->commit(); // トランザクション終了

    header('Location: ../sales/sales_manegement.php');
    exit();

  } catch (PDOException $e) {
    $db->rollBack(); // エラー発生時ロールバック
    exit('エラー：'.$e->getMessage());
  }
}
?>


<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>確認</title><!-- 全画面構成図:ファイル名 -->
      <!-- Bootstrap CSSの読み込み -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
      <link rel="stylesheet" href="../../css/style.css">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <!-- Googleフォントデフォルト -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
       <!-- Googleフォントデフォルト -->
  </head>
  <body>
      <header>
      <!-- Bootstrap JavaScriptの読み込み -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
      <!-- navbar.phpここから -->
        <?php include('../navbar.php') ?>
      <!-- navbar.phpここまで -->
      </header>


      <main>

      </main>
      <footer>
            
      </footer>               
  </body>
</html>
