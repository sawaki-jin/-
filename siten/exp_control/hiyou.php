<?php
session_start();

//DBに接続
$dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
$user = 'root';
$password = '';

try {
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    if (isset($_POST['yatin'])) {
        $tenpo_no = $_SESSION['ep_no']; // 店舗番号をセッションから取得
        $yatin = $_POST['yatin'];
        $genkashokyakuhi = $_POST['shoukyakuhi'];
        $siharairisoku = $_POST['siharairisoku'];
        $risuryou = $_POST['ri-suryou'];
        $tuusinhi = $_POST['tuusinhi'];
        $hokenryou = $_POST['hokenryou'];
        $koteihisonota = $_POST['sonota1'];
        $shokuzaihi = $_POST['shokuzaihi'];
        $jinkenhi = $_POST['jinkenhi'];
        $suidoukounetuhi = $_POST['suidoukounetuhi'];
        $hanbaisokusinhi = $_POST['hanbaisokusinhi'];
        $housouhi = $_POST['housaihi'];
        $hendouhisonota = $_POST['sonota2'];

        // データの存在チェック
        $stmt = $db->prepare("
            SELECT 1 FROM exp_control
            WHERE tenpo_no = :tenpo_no
            AND salesdate = CURRENT_DATE
        ");

        $stmt->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_INT);
        $stmt->execute();

        // データが存在する場合
        if ($stmt->fetchColumn()) {
            $sql = "
                UPDATE exp_control
                SET 
                    chidaiyatin = :yatin,
                    genkashokyakuhi = :genkashokyakuhi,
                    siharairisoku = :siharairisoku,
                    risuryou = :risuryou,
                    tuusinhi = :tuusinhi,
                    hokenryou = :hokenryou,
                    koteihisonota = :koteihisonota,
                    shokuzaihi = :shokuzaihi,
                    jinkenhi = :jinkenhi,
                    suidoukounetuhi = :suidoukounetuhi,
                    hanbaisokusinhi = :hanbaisokusinhi,
                    housouhi = :housouhi,
                    hendouhisonota = :hendouhisonota
                WHERE tenpo_no = :tenpo_no
                AND salesdate = CURRENT_DATE;
            ";
        } else {
            // データが存在しない場合
            $sql = "
                INSERT INTO exp_control (
                    tenpo_no, salesdate, uriage,
                    chidaiyatin, genkashokyakuhi, siharairisoku, risuryou, tuusinhi, hokenryou, koteihisonota,
                    shokuzaihi, jinkenhi, suidoukounetuhi, hanbaisokusinhi, housouhi, hendouhisonota
                )
                VALUES (
                    :tenpo_no, CURRENT_DATE, 0,
                    :yatin, :genkashokyakuhi, :siharairisoku, :risuryou, :tuusinhi, :hokenryou, :koteihisonota,
                    :shokuzaihi, :jinkenhi, :suidoukounetuhi, :hanbaisokusinhi, :housouhi, :hendouhisonota
                )
            ";
        }

        $stmt = $db->prepare($sql);

        // パラメータのバインド
        $stmt->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_INT);
        $stmt->bindParam(':yatin', $yatin, PDO::PARAM_INT);
        $stmt->bindParam(':genkashokyakuhi', $genkashokyakuhi, PDO::PARAM_INT);
        $stmt->bindParam(':siharairisoku', $siharairisoku, PDO::PARAM_INT);
        $stmt->bindParam(':risuryou', $risuryou, PDO::PARAM_INT);
        $stmt->bindParam(':tuusinhi', $tuusinhi, PDO::PARAM_INT);
        $stmt->bindParam(':hokenryou', $hokenryou, PDO::PARAM_INT);
        $stmt->bindParam(':koteihisonota', $koteihisonota, PDO::PARAM_INT);
        $stmt->bindParam(':shokuzaihi', $shokuzaihi, PDO::PARAM_INT);
        $stmt->bindParam(':jinkenhi', $jinkenhi, PDO::PARAM_INT);
        $stmt->bindParam(':suidoukounetuhi', $suidoukounetuhi, PDO::PARAM_INT);
        $stmt->bindParam(':hanbaisokusinhi', $hanbaisokusinhi, PDO::PARAM_INT);
        $stmt->bindParam(':housouhi', $housouhi, PDO::PARAM_INT);
        $stmt->bindParam(':hendouhisonota', $hendouhisonota, PDO::PARAM_INT);

        $stmt->execute();

        header('Location: koteihi.php');
        exit();
    } 

} catch (PDOException $e) {
    exit('エラー：' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>飲食店向け 固定費・変動費 入力フォーム</title>
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
        .form-page {
            width: 100%;
            max-width: 700px; /* フォームの幅を調整 */
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
        .form table {
            width: 100%;
            margin-bottom: 20px;
        }
        .form th, .form td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .form input[type="number"] {
            font-family: "Roboto", sans-serif;
            outline: 0;
            background: #f2f2f2;
            width: 100%;
            border: 0;
            padding: 10px;
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
    </style>
</head>
<body>
<div class="form-page">
    <div class="form">
        <h1>飲食店向け 固定費・変動費 入力フォーム</h1>
        <form action="" method="post">
            <h2>固定費</h2>
            <table>
                <tr>
                    <th>項目</th>
                    <th>金額</th>
                </tr>
                <tr>
                    <td>地代家賃</td>
                    <td><input type="number" name="yatin" required></td>
                </tr>
                <tr>
                    <td>減価償却費</td>
                    <td><input type="number" name="shoukyakuhi" required></td>
                </tr>
                <tr>
                    <td>支払利息</td>
                    <td><input type="number" name="siharairisoku" required></td>
                </tr>
                <tr>
                    <td>リース料</td>
                    <td><input type="number" name="ri-suryou" required></td>
                </tr>
                <tr>
                    <td>通信費</td>
                    <td><input type="number" name="tuusinhi" required></td>
                </tr>
                <tr>
                    <td>保険料</td>
                    <td><input type="number" name="hokenryou" required></td>
                </tr>
                <tr>
                    <td>その他</td>
                    <td><input type="number" name="sonota1" required></td>
                </tr>
            </table>

            <h2>変動費</h2>
            <table>
                <tr>
                    <th>項目</th>
                    <th>金額</th>
                </tr>
                <tr>
                    <td>食材費</td>
                    <td><input type="number" name="shokuzaihi" required></td>
                </tr>
                <tr>
                    <td>人件費</td>
                    <td><input type="number" name="jinkenhi" required></td>
                </tr>
                <tr>
                    <td>水道光熱費</td>
                    <td><input type="number" name="suidoukounetuhi" required></td>
                </tr>
                <tr>
                    <td>販売促進費</td>
                    <td><input type="number" name="hanbaisokusinhi" required></td>
                </tr>
                <tr>
                    <td>包装費</td>
                    <td><input type="number" name="housaihi" required></td>
                </tr>
                <tr>
                    <td>その他</td>
                    <td><input type="number" name="sonota2" required></td>
                </tr>
            </table>
            <button type="submit">送信</button>
        </form>
    </div>
</div>
</body>
</html>