<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// データベース接続情報
$dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
$user = 'root';
$password = '';

try {
    // PDOインスタンスの生成
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // ユーザー名の取得
    if (isset($_SESSION['ep_no'])) {
        $stmt = $db->prepare("SELECT ep_name FROM EMPLOYEE WHERE ep_no = ?");
        $stmt->execute([$_SESSION['ep_no']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $username = $result ? $result['ep_name'] : '名前が見つかりません';
    } else {
        $username = 'ゲスト';
    }

    // 今日更新、登録されたニュースがあるかを確認
    $today = date('Y-m-d');
    $news_stmt = $db->prepare("SELECT COUNT(*) FROM news_articles WHERE DATE(created_at) = ? OR DATE(updated_at) = ?");
    $news_stmt->execute([$today, $today]);
    $news_count = $news_stmt->fetchColumn();

    // 画像の名前を決定
    $image_name = ($news_count > 0) ? 'bellari2.png' : 'bellnasi2.png';
    
} catch (PDOException $e) {
    exit('データベースエラー：' . $e->getMessage());
}

// 現在のページのURLを取得
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>経営管理システム</title>
    <link rel="stylesheet" href="navbar_ue.css" type="text/css">
    <link rel="shortcut icon" href="../images/favicon.ico">
</head>
<body>
    <div class="header-container">
        <div class="header-left">
            <div class="logo">
                <a href="index.php">
                    <img src="../images/月影庵.png" style="width:100px;height:auto;" alt="月影庵ロゴ">
                </a>
            </div>
            <a href="index.php">
                <h1 style="font-family:serif;">経営管理システム</h1>
            </a>
        </div>
        <div class="header-right">
            <div class="user-info">
                <span class="username" style="font-size:20px;"><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>さん</span>
                <div class="user-dropdown" style="display:none;">
                    <?php
                    if (isset($_SESSION['ep_no'])) {
                        $stmt = $db->prepare("SELECT e.ep_no, e.ep_name, e.yakusyoku, t.tenpo_name 
                                              FROM EMPLOYEE e 
                                              LEFT JOIN TENPO t ON e.tenpo_no = t.tenpo_no 
                                              WHERE e.ep_no = ?");
                        $stmt->execute([$_SESSION['ep_no']]);
                        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($user_info) {
                            echo "<p>従業員番号: " . htmlspecialchars($user_info['ep_no'], ENT_QUOTES, 'UTF-8') . "</p>";
                            echo "<p>名前: " . htmlspecialchars($user_info['ep_name'], ENT_QUOTES, 'UTF-8') . "</p>";
                            echo "<p>役職: " . htmlspecialchars($user_info['yakusyoku'], ENT_QUOTES, 'UTF-8') . "</p>";
                            echo "<p>店舗: " . htmlspecialchars($user_info['tenpo_name'], ENT_QUOTES, 'UTF-8') . "</p>";
                        }
                    }
                    ?>
                </div>
            </div>
            <!-- 画像リンクの追加 -->
            <a href="news/news_list.php" class="header-image-link">
                <img src="../images/<?php echo htmlspecialchars($image_name, ENT_QUOTES, 'UTF-8'); ?>" alt="リンク画像" class="header-image">
            </a>
            <form action="login_out.php" method="post" style="display:inline;">
                <button type="submit" name="logout" class="logout-button">ログアウト</button>
            </form>
        </div>
    </div>
    <nav>
        <ul>
            <li<?php echo $current_page == 'index.php' ? ' class="current"' : ''; ?>>
                <a href='index.php' class="nav-item">
                    <img src='../images/home.png' alt='トップ画面' class='nav-icon'> トップ画面
                </a>
            </li>
            <li class="dropdown<?php echo in_array($current_page, ['product_list.php', 'product_change.php', 'product_add.php']) ? ' current' : ''; ?>">
                <span class="nav-link">
                    <img src='../images/yakitori.png' alt='商品管理' class='nav-icon'> 商品管理
                </span>
                <div class="dropdown-content">
                    <a href='commodity/product_list.php'>商品リスト</a>
                    <a href='commodity/product_add.php'>商品追加</a>
                </div>
            </li>
            <li class="dropdown<?php echo in_array($current_page, ['human_affairs.php', 'new_login.php']) ? ' current' : ''; ?>">
                <span class="nav-link">
                    <img src='../images/group.png' alt='人事' class='nav-icon'> 人事
                </span>
                <div class="dropdown-content">
                    <a href='human_affairs/human_affairs list.php'>人事管理</a>
                    <a href='human_affairs/new_login.php'>新規登録</a>
                </div>
            </li>
            <li class="dropdown<?php echo in_array($current_page, ['list.php', 'htouka.php']) ? ' current' : ''; ?>">
                <span class="nav-link">
                    <img src='../images/store.png' alt='店舗一覧' class='nav-icon'> 店舗一覧
                </span>
                <div class="dropdown-content">
                    <a href='store/list.php'>店舗リスト</a>
                    <a href='store/add.php'>店舗統合</a>
                </div>
            </li>
            <li class="dropdown<?php echo in_array($current_page, ['contact_instruction.php', 'contact_reception.php']) ? ' current' : ''; ?>">
                <span class="nav-link">
                    <img src='../images/siji.png' alt='指示' class='nav-icon'> 指示
                </span>
                <div class="dropdown-content">
                    <a href='contact/contact_instruction.php'>指示送信</a>
                    <a href='contact/contact_reception.php'>受付管理</a>
                </div>
            </li>
            <li<?php echo $current_page == 'manegement.php' ? ' class="current"' : ''; ?>>
                <a href='manegement/manegement.php' class="nav-item">
                    <img src='../images/building.png' alt='会社情報' class='nav-icon'> 会社情報
                </a>
            </li>
            <li<?php echo $current_page == 'graph.php' ? ' class="current"' : ''; ?>>
                <a href='graph/graph.php' class="nav-item">
                    <img src='../images/graph.png' alt='利益分析' class='nav-icon'> 利益分析
                </a>
            </li>
            <li<?php echo $current_page == 'document.php' ? ' class="current"' : ''; ?>>
                <a href='document/document.php' class="nav-item">
                    <img src='../images/document.png' alt='資料' class='nav-icon'> 資料
                </a>
            </li>
        </ul>
    </nav>

    <script src="navbar_ue.js"></script>
</body>
</html>
