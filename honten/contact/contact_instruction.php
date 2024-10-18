<?php include '../login_check.php'; ?>
<?php
// データベース接続情報
$dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
$user = 'root';

$message = "";  // 初期化

try {
    $pdo = new PDO($dsn, $user);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $contact_type = $_POST['contact_type'] ?? '';
        $contact_content = '';
        $contactmoto_no = $_POST['contactmoto_no'] ?? 0;
        $contactsaki_no = $_POST['contactsaki_no'] ?? '';

        if ($contact_type === '指示') {
            $contact_content = " 指示名:" . ($_POST['指示名'] ?? '') . " 指示内容:" . ($_POST['指示内容'] ?? '');
        } elseif ($contact_type === '連絡') {
            $contact_content = $_POST['contact_content'] ?? '';
        }

        $sql = "INSERT INTO contact (contactsaki_no, contactmoto_no, contact_type, contact_content)
                VALUES (:contactsaki_no, :contactmoto_no, :contact_type, :contact_content)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':contactsaki_no', $contactsaki_no);
        $stmt->bindParam(':contactmoto_no', $contactmoto_no);
        $stmt->bindParam(':contact_type', $contact_type);
        $stmt->bindParam(':contact_content', $contact_content);
        $stmt->execute();

        $message = "<div class='alert alert-success mt-3'>データベースにデータを登録しました。</div>";
    }
} catch (PDOException $e) {
    $message =  "<div class='alert alert-danger mt-3'>エラー: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>月影庵</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body {
            font-family: 'Noto Serif JP', serif;
            color: #333;
        }
        .navbar {
            background-color: #8d6e63;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #ffffff !important;
        }
        .container {
            max-width: 700px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: bold;
            color: #5d4037;
        }
        #contactForm {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include('../navbar.php');?>

    <div class="container">
        <form method="post" action="contact_instruction.php" id="contactForm">
            <div class="mb-3">
                <h2>指示/連絡</h2>
                <?php echo $message; ?>
                <label for="contact_type" class="form-label">連絡種類:</label>
                <select name="contact_type" id="contactType" class="form-select">
                    <option value="-">-</option>
                    <option value="指示">指示</option>
                    <option value="連絡">連絡</option>
                </select>
            </div>
            <div id="指示フィールド" class="mb-3" style="display: none;">
                <label for="指示名" class="form-label">指示名:</label>
                <input type="text" name="指示名" class="form-control">
                <br>
                <label for="contactsaki_no" class="form-label">連絡先店舗番号:</label>
                <input type="text" name="contactsaki_no" class="form-control">
                <br>
                <label for="指示内容" class="form-label">指示内容:</label>
                <textarea name="指示内容" class="form-control"></textarea>
            </div>
            <div id="連絡フィールド" class="mb-3" style="display: none;">
                <label for="contact_content" class="form-label">連絡内容:</label>
                <textarea name="contact_content" class="form-control"></textarea>
            </div>
            <input type="hidden" name="contactmoto_no" value="1">
            <button type="submit" class="btn btn-primary">送信</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contactType = document.getElementById('contactType');
            const 指示フィールド = document.getElementById('指示フィールド');
            const 連絡フィールド = document.getElementById('連絡フィールド');

            const toggleFields = () => {
                const value = contactType.value;
                指示フィールド.style.display = value === '指示' ? 'block' : 'none';
                連絡フィールド.style.display = value === '連絡' ? 'block' : 'none';
            };

            contactType.addEventListener('change', toggleFields);
            toggleFields(); // 初期状態の設定
        });
    </script>

    <?php include("../footer.php");?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>