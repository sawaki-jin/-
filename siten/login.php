<!DOCTYPE html>
<html lang="en">
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
<div class="login-page">
    <div class="form">
        <h1>ログイン画面</h1>
        <form class="login-form" action="login code.php" method="post">
            <div class="text1">
                <label for="ep_no">従業員番号</label>
                <input type="text" id="ep_no" name="ep_no" placeholder="例)10001" required>
            </div>
            <div class="text1">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" placeholder="パスワード" required>
            </div>
            <button class="button" type="submit">ログイン</button>
        </form>
    </div>
</div>
</body>
</html>
