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
    if(isset($_SESSION['ep_no'])) {
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
    <link rel="stylesheet" href="../navbar.css" type="text/css">
    <link rel="shortcut icon" href="../../images/favicon.ico">
    <style>
        /* ドロップダウンメニューのスタイル */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* ナビゲーションアイコンのスタイル */
        .nav-icon {
            height: 1em;
            width: auto;
            margin-right: 8px;
        }

        /* ホバー時のエフェクトを統一 */
        .nav-item, .nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 0;
            text-decoration: none;
            color: #808080;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-item:hover, .nav-link:hover {
            background-color: #e6e6fa;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="header-left">
            <div class="logo">
                <a href="../index.php">
                    <img src="../../images/月影庵.png" style="width:100px;height:auto;" alt="月影庵ロゴ">
                </a>
            </div>
            <a href="../index.php">
                <h1 style="font-family:serif;">経営管理システム</h1>
            </a>
        </div>
        <div class="header-right">
            <div class="user-info">
                <span class="username" style="font-size:20px;"><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>さん</span>
                <div class="user-dropdown" style="display:none;">
                    <?php
                    if(isset($_SESSION['ep_no'])) {
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
            <a href="../news/news_list.php" class="header-image-link">
                <img src="../../images/<?php echo htmlspecialchars($image_name, ENT_QUOTES, 'UTF-8'); ?>" alt="リンク画像" class="header-image">
            </a>
            <form action="../login_out.php" method="post" style="display:inline;">
                <button type="submit" name="logout" class="logout-button">ログアウト</button>
            </form>
        </div>
    </div>
    <nav>
        <ul>
            <li<?php echo $current_page == 'index.php' ? ' class="current"' : ''; ?>>
                <a href='../index.php' class="nav-item">
                    <img src='../../images/home.png' alt='トップ画面' class='nav-icon'> トップ画面
                </a>
            </li>
            <li<?php echo $current_page == 'graph.php' ? ' class="current"' : ''; ?>>
                <a href='../graph/graph.php' class="nav-item">
                    <img src='../../images/graph.png' alt='グラフ' class='nav-icon'> グラフ
                </a>
            </li>
            <li class="dropdown<?php echo $current_page == 'exp_control.php' ? ' current' : ''; ?>">
                <span class="nav-link">
                    <img src='../../images/salary.png' alt='支出管理' class='nav-icon'> 支出管理
                </span>
                <div class="dropdown-content">
                    <a href='../exp_control/input.php'>入力</a>
                    <a href='../exp_control/hiyou.php'>費用</a>
                    <a href='../exp_control/koteihi.php'>固定費</a>
                    <a href='../exp_control/hendouhi.php'>変動費</a>
                </div>
            </li>
            <li class="dropdown<?php echo $current_page == 'salse.php' ? ' current' : ''; ?>">
                <span class="nav-link">
                    <img src='../../images/yakitori.png' alt='販売管理' class='nav-icon'> 販売管理
                </span>
                <div class="dropdown-content">
                    <a href='../sales/product_list.php'>商品リスト</a>
                    <a href='../sales/sales_manegement.php'>販売管理</a>
                </div>
            </li>
            <li class="dropdown<?php echo $current_page == 'contact.php' ? ' current' : ''; ?>">
                <span class="nav-link">
                    <img src='../../images/mail.png' alt='本部連絡' class='nav-icon'> 本部連絡
                </span>
                <div class="dropdown-content">
                    <a href='../contact/contact_send.php'>送信</a>
                    <a href='../contact/contact_reception.php'>リスト</a>
                </div>
            </li>
            <li<?php echo $current_page == 'employee.php' ? ' class="current"' : ''; ?>>
                <a href='../employee/employee.php' class="nav-item">
                    <img src='../../images/group.png' alt='人事' class='nav-icon'> 人事
                </a>
            </li>
            <li<?php echo $current_page == 'document.php' ? ' class="current"' : ''; ?>>
                <a href='../document/document.php' class="nav-item">
                    <img src='../../images/document.png' alt='資料' class='nav-icon'> 資料
                </a>
            </li>
        </ul>
    </nav>

    <script src="../navbar.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdowns = document.querySelectorAll('.dropdown');

        dropdowns.forEach(function(dropdown) {
            var dropdownContent = dropdown.querySelector('.dropdown-content');

            // 親項目の幅を取得し、ドロップダウンメニューに適用
            var parentWidth = dropdown.offsetWidth;
            dropdownContent.style.minWidth = parentWidth + 'px';
            
            dropdown.addEventListener('mouseover', function() {
                dropdownContent.style.display = 'block';
            });

            dropdown.addEventListener('mouseout', function() {
                dropdownContent.style.display = 'none';
            });
        });
    });
    </script>
</body>
</html>
