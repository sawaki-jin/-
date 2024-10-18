<?php include '../login_check.php'; ?>
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
        .login-page {
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
        .form input, .form select {
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
<div class="login-page">
    <div class="form">
        <form class="login-form" action="register.php" method="post">
            <h1>新規従業員登録</h1>
            <div class="text1">
                <label for="tenpo_no">店舗番号、店舗名、エリア名</label>
                <select id="tenpo_no" name="tenpo_no" required>
                    <option value="" disabled selected>選択してください</option>
                    <?php
                    // データベース接続情報
                    $dsn = "mysql:dbname=bbadb;host=localhost;charset=utf8mb4";
                    $username = "root";
                    $password = ""; // 必要に応じてパスワードを設定してください
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ];

                    try {
                        // データベース接続を作成
                        $pdo = new PDO($dsn, $username, $password, $options);

                        // `tenpo`テーブルからデータを取得
                        $sql = "SELECT tenpo_no, tenpo_name, erea_name FROM tenpo";
                        $stmt = $pdo->query($sql);

                        // データをループして<option>タグを生成
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['tenpo_no']}'>{$row['tenpo_no']} - {$row['tenpo_name']} - {$row['erea_name']}</option>";
                        }

                        // `employee`テーブルから最大従業員番号を取得
                        $sql = "SELECT MAX(ep_no) AS max_ep_no FROM employee";
                        $stmt = $pdo->query($sql);
                        $result = $stmt->fetch();
                        $new_ep_no = $result['max_ep_no'] + 1;

                    } catch (PDOException $e) {
                        echo "<div class='alert alert-danger' role='alert'>接続に失敗しました: " . $e->getMessage() . "</div>";
                    }
                    ?>
                </select>

            </div>
            <input type = "hidden" id="ep_no" name="ep_no" value="<?php echo $new_ep_no; ?>" readonly required>
            <label for="ep_no">従業員番号　<?php echo $new_ep_no ?></label>
            <p style = "text-align: left;color:red;">※従業員番号はログイン時に必要です</p>
            <div class="text1">
                <label for="ep_name">従業員名</label>
                <input type="text" id="ep_name" name="ep_name" required>
                </div>
            <div class="text1">
                <label for="yakusyoku">役職</label>
                <select id="yakusyoku" name="yakusyoku" required>
                    <option value="" disabled selected>選択してください</option>
                    <option value="管理者">1 - 管理者</option>
                    <option value="エリアマネージャー">2 - エリアマネージャー</option>
                    <option value="店長">3 - 店長</option>
                    <option value="社員">4 - 社員</option>
                    <option value="バイト">5 - バイト</option>
                </select>
            </div>
            <div class="text1">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button class="button" type="submit">登録</button>
        </form>
    </div>
</div>
</body>
</html>
