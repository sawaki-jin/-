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
        $contactsaki_no = $_POST['contactsaki_no'] ?? 0;

        if ($contact_type === '申請') {
            $contact_content = " 申請名:" . ($_POST['申請名'] ?? '') . " 所属店舗番号:" . ($_POST['所属店舗番号'] ?? '') . " 役職:" . ($_POST['役職'] ?? '') . " パスワード:" . ($_POST['パスワード'] ?? '');
        } elseif ($contact_type === '報告') {
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
        <form method="post" action="contact_send.php" id="contactForm">
            <div class="mb-3">
                <h2>申請/報告</h2>
                <?php echo $message; ?>
                <label for="contact_type" class="form-label">連絡種類:</label>
                <select name="contact_type" id="contactType" class="form-select">
                    <option value="-">-</option>
                    <option value="申請">申請</option>
                    <option value="報告">報告</option>
                </select>
            </div>
            <div id="申請フィールド" class="mb-3" style="display: none;">
                <label for="申請名" class="form-label">申請名:</label>
                <input type="text" name="申請名" class="form-control">
                <br>
                <label for="所属店舗番号" class="form-label">所属店舗番号:</label>
                <input type="text" name="所属店舗番号" class="form-control" pattern="^[0-9]+$">
                <br>
                <label for="役職" class="form-label">役職:</label>
                <input type="text" name="役職" class="form-control">
                <br>
                <label for="パスワード" class="form-label">パスワード:</label>
                <input type="password" name="パスワード" class="form-control">
            </div>
            <div id="報告フィールド" class="mb-3" style="display: none;">
                <label for="contact_content" class="form-label">連絡内容:</label>
                <textarea name="contact_content" class="form-control"></textarea>
            </div>
            <input type="hidden" name="contactsaki_no" value="1">
            <input type="hidden" name="contactmoto_no" value="<?php echo $_SESSION['ep_no']; ?>">
            <button type="submit" class="btn btn-primary">送信</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contactType = document.getElementById('contactType');
            const 申請フィールド = document.getElementById('申請フィールド');
            const 報告フィールド = document.getElementById('報告フィールド');

            const toggleFields = () => {
                const value = contactType.value;
                申請フィールド.style.display = value === '申請' ? 'block' : 'none';
                報告フィールド.style.display = value === '報告' ? 'block' : 'none';
            };

            contactType.addEventListener('change', toggleFields);
            toggleFields(); // 初期状態の設定
        });
    </script>

    <?php include("../footer.php");?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
