<?php include 'login_check_ue.php'; ?>
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

// ニュース記事をデータベースから取得
$newsArticles = [];
try {
  $sql = "SELECT article_id, title, store, DATE_FORMAT(created_at, '%Y-%m-%d') as created_date FROM news_articles ORDER BY article_id DESC LIMIT 4";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $newsArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $today = date('Y-m-d');
} catch (Exception $e) {
    echo "<div class='login-page'><div class='form'><h1>ニュース記事の取得に失敗しました: " . $e->getMessage() . "</h1></div></div>";
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <title>ホームページ本店</title>
  <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
        }
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      .slider-container {
        width: 94%;
        margin: 0 auto;
      }

      .slider {/*横幅94%で左右に余白を持たせて中央寄せ*/
          width:94%;
          margin:0 auto;
      }

      .slider img {
          width:60vw;/*スライダー内の画像を60vwにしてレスポンシブ化*/
          height:auto;
      }

      .slider .slick-slide {
        transform: scale(0.8);/*左右の画像のサイズを80%に*/
        transition: all .5s;/*拡大や透過のアニメーションを0.5秒で行う*/
        opacity: 0.5;/*透過50%*/
      }

      .slider .slick-slide.slick-center{
        transform: scale(1);/*中央の画像のサイズだけ等倍に*/
        opacity: 1;/*透過なし*/
      }


      /*矢印の設定*/

      /*戻る、次へ矢印の位置*/
      .slick-prev, 
      .slick-next {
          position: absolute;/*絶対配置にする*/
          top: 42%;
          cursor: pointer;/*マウスカーソルを指マークに*/
          outline: none;/*クリックをしたら出てくる枠線を消す*/
          border-top: 2px solid #666;/*矢印の色*/
          border-right: 2px solid #666;/*矢印の色*/
          height: 15px;
          width: 15px;
      }

      .slick-prev {/*戻る矢印の位置と形状*/
          left: -1.5%;
          transform: rotate(-135deg);
      }

      .slick-next {/*次へ矢印の位置と形状*/
          right: -1.5%;
          transform: rotate(45deg);
      }



      ul{
        margin:0;
        padding: 10px;
        list-style: none;
      }



        .suraido {
            width: 100%;
            height: auto;
            aspect-ratio: 16 / 9; /* Set the aspect ratio to 16:9 */
        }



        .notice-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        .notice-table th, .notice-table td {
            border-bottom: 1px solid #add8e6; /* 薄い青色の下線 */
            padding: 10px;
            text-align: left;
        }

        .notice-title {
            font-size: 24px;
            margin: 40px 0 0 10%;
            text-align: left; /* 左寄せ */
        }

        .nav-buttons {
          display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin: 20px 10%;
        
        }

        .nav-buttons a {
            text-decoration: none;
        }


        .nav-buttons a:active {
            transform: translateY(0);
            box-shadow: none;
        }
        .link-description {
            margin: 0 0 20px 20px;
            font-size: 16px;
            text-align: left;
        }
        a {
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        a.hover-sample:hover{
            color: #FF0000;
            font-weight: bold;

        }
        .newslist{
          margin:auto 160px auto 160px ;
        }
        .title-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin: 15px auto ;
            
            
        }
        .title-border{

          border-top: solid 1px #9f9f9f;
          border-bottom: solid 1px #9f9f9f;
        }
        .date {
            color: #6c757d;
            flex-shrink: 0;
            margin-right: 10px;
            font-size: 15px;
        }
        .store {
            color: #333;
            font-weight: bold;
            padding: 3px 30px;
            border: 1.5px solid #333;
            border-radius: 20px;
            background-color: #f2f2f2;
            margin-right: 20px;
            font-size: 20px;
        }
        .store.main-office {
            color: #007bff;
            font-weight: bold;
        }

        .title-text {
            flex-grow: 1;
            display: flex;
            align-items: center;
        }

        #opv-wrap {
          display: none;
        }

        video#opv {
          position: fixed;
          top: 0;
          left: 0;
          object-fit: cover;
          width: 100vw;
          height: 100vh;
          vertical-align: bottom;
        }

        main {
          display: none;
        }

        #mv {
          height: 100vh;
          background-color: rgb(216, 251, 254);
          display: flex;
          justify-content: center;
          align-items: center;
        }
        .new-flag {
            color: red;
            font-weight: bold;
            margin-left: 10px;
        }
        .koumoku1{
          width: 500px;
          height:500px;
          float: left;
          text-align: center;
          position: relative;
        }
        .koumoku1 img { width: 100%; }
        .koumoku2{
          width: 500px;
          height:500px;
          float: right;
          text-align: center;
          position: relative;
        }
        .koumoku2 img { width: 100%; }
        .koumoku3{
          width: 500px;
          height: 500px;
          float: left;
          text-align: center;
          position: relative;
        }
        .koumoku3 img { width: 100%; }
        .koumoku4{
          width: 500px;
          height:500px;
          float: right;
          text-align: center;
          position: relative;
        }
        .koumoku4 img { width: 100%; }
        .koumoku5{
          width: 500px;
          height:500px;
          float: left;
          text-align: center;
          position: relative;
        }
        .koumoku5 img { width: 100%; }
        .koumoku6{
          width: 500px;
          height:500px;
          float: right;
          text-align: center;
          position: relative;
        }
        .koumoku6 img { width: 100%; }
        .nakami{
          position: absolute; /* 追加 */
          top: 60%; /* 上端位置の調整 */
          left: 50%; /* 左端位置の調整 */
          transform: translate(-50%, -50%); /* 中央揃え */
          width: auto;
          max-width: 90%; /* 幅の調整 */
          padding: 20px; /* パディングの追加 */
          background-color: #f2f2f2;
          z-index: 1; /* 他の要素より前面に表示 */
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(function(){
      // 動画を再生するかのフラグ
      let firstVisit = localStorage.getItem('firstVisit');
      if (firstVisit === null) {
        // 初回アクセス時の処理
        localStorage.setItem('firstVisit', 'done'); // フラグをセット

        const opVideo = $('#opv').get(0);
        const playTime = 4000; // 4秒間再生

        $('#opv-wrap').fadeIn(1000);
        opVideo.play(); // 動画を再生

        setTimeout(function(){
          $('#opv-wrap').fadeOut(1000, function(){
            $('main').fadeIn(1000, initializeSlider); // フェードイン後にスライドショー初期化
            opVideo.pause(); // フェードアウト後に動画を停止
          });
        }, playTime);

      } else {
        // 2回目以降のアクセス時の処理
        $('main').fadeIn(1000, initializeSlider); // フェードイン後にスライドショー初期化
      }
    });
  </script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
</head>
<body>
      <!-- 画面遷移動画 -->
  <div id="opv-wrap">
    <video id="opv" autoplay muted>
      <source src="../images/atlanta.mp4" type="video/mp4">
    </video>
  </div>

  <main>

    <?php include 'navbar_ue.php'; ?>
    <div class="slider-container">
      <ul class="slider">
      <li>
          <img class = "suraido"src="../images/suraido.jpg" alt="content1">
      </li>
      <li>
        <img class = "suraido" src="../images/suraido2.jpg" alt="content1">
      </li>
      <li>
        <img class = "suraido" src="../images/suraido3.jpg" alt="content1">
      </li>
      <li>
        <img class = "suraido" src="../images/suraido4.jpg" alt="content1">
      </li>
    </div>
    <div class="news">
        <h2 class="notice-title"><<ニュース>></h2>
        <ul class = "newslist">
            <?php
            if (!empty($newsArticles)) {
                foreach ($newsArticles as $title) {
                    $article_id = htmlspecialchars($title['article_id'], ENT_QUOTES, 'UTF-8');
                    $titleText = htmlspecialchars($title['title'], ENT_QUOTES, 'UTF-8');
                    $store = htmlspecialchars($title['store'], ENT_QUOTES, 'UTF-8');
                    $createdAt = htmlspecialchars($title['created_date'], ENT_QUOTES, 'UTF-8');
                    $storeClass = (strpos($store, '本社') !== false) ? 'main-office' : '';

                    $newFlag = ($createdAt === $today) ? "<span class='new-flag'>new‼</span>" : "";

                    echo "<li style = 'list-style:none;'><a class='hover-sample' style='text-decoration:none;' href='news/news_details.php?article_id=$article_id'>
                          <div class='title-border'>
                            <div class='title-container'>
                                <span class='date'>$createdAt</span>
                                <span class='store $storeClass'>$store</span>
                                <span class='title-text'>$titleText $newFlag</span>
                            </div>
                          </div>
                          </a></li>";
                }
            } else {
                echo "<p>ニュース記事がありません。</p>";
            }
            ?>
        </ul>
    </div>
    <div>
        <h2 class="notice-title"><<各リンク紹介>></h2>
    </div>
    <div class="nav-buttons">
        <div>
          <div class="koumoku1">
            <a href="commodity/product_list.php">
            <img src="../images/syouhinkanri.jpeg">
            <div class = nakami>
              商品管理<br>商品管理ページでは、在庫の管理や商品の追加、編集を行うことができます。</a>
            </div>
          </div>
          <div class = koumoku2>
            <a href="human_affairs/human_affairs list.php">
            <img src="../images/jinji.jpeg">
            <div class = nakami>
              人事<br>人事ページでは、従業員の管理や新しい従業員の追加、情報の更新を行うことができます。</a>
          </div>
          </div>
          <div class="koumoku3">
            <a href="store/list.php">
              <img src="../images/tenpoitiran.jpg">
              <div class = nakami>  
              店舗一覧<br>店舗一覧ページでは、全店舗の情報を閲覧することができ、各店舗の詳細情報も確認できます。
              </div>
            </a>
          </div>
          <div class = koumoku4>
            <a href="contact/contact_reception.php">
            <img src="../images/siji.jpg">
            <div class = nakami>
              指示<br>指示ページでは、重要な連絡事項や指示を全従業員に共有することができます。</a>
          </div>
          </div>
          <div class = koumoku5>
            <a href="manegement/manegement.php">
            <img src="../images/kaisyazentai.jpg">
            <div class = nakami>
              会社全体<br>会社全体ページでは、企業の概要や全体の方針、主要な連絡先などを確認することができます。</a>
          </div>
          </div>
          <div class = koumoku6>
            <a href="graph/graph.php">
            <img src="../images/riekibunseki.jpg">
            <div class = nakami>
              利益分析<br>利益分析ページでは、売上や利益のデータをグラフで視覚的に確認し、分析することができます。</a>
          </div>
          </div>
        </div>
    </div>
  </main>
  <script>
  window.embeddedChatbotConfig = {
  chatbotId: "uHbqV_pNmW2lftm9lUCzp",
  domain: "www.chatbase.co"
  }
  </script>
  <script
  src="https://www.chatbase.co/embed.min.js"
  chatbotId="uHbqV_pNmW2lftm9lUCzp"
  domain="www.chatbase.co"
  defer>
  </script>
    <script>
    function initializeSlider() {
      $('.slider').slick({
        autoplay: true,
        autoplaySpeed: 5000, // 5秒間隔で自動スライド
        infinite: true,
        speed: 500,
        slidesToShow: 3,
        slidesToScroll: 1,
        prevArrow: '<div class="slick-prev"></div>',
        nextArrow: '<div class="slick-next"></div>',
        centerMode: true,
        variableWidth: true,
        dots: true,
      });
    }
    </script>
</body>
<footer>
<?php include 'footer_ue.php'; ?>
  </footer>
</html>