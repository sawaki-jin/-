<?php include '../login_check.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>月影庵 - 在庫管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body {
            background-color: #fff;
        }
        .navbar {
            background-color: #343a40;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 30px;
            max-width: 1000px;
        }
        h2 {
            color: #343a40;
            border-bottom: 2px solid #343a40;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .table {
            box-shadow: 0 0 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .table thead {
            background-color: #343a40;
            color: white;
        }
        #genre {
            max-width: 200px;
        }
        .table td {
            vertical-align: middle;
            border-right: 1px solid #dee2e6; /* 縦線を追加 */
        }
        .table td:last-child { /* 最後の列の縦線を消す */
            border-right: none;
        }
        /* 縦線をth,trまで伸ばす */
        .table thead th {
            border-right: 1px solid #dee2e6;
        }
        .table tbody tr {
            border-bottom: 1px solid #dee2e6;
        }
        .action-buttons {
            display: flex;
            justify-content: space-evenly;
        }
        .action-buttons form {
            display: inline;
        }
        .wider-input {
            width: 160px; /* または希望の幅 */
        }
    </style>
</head>
<body>
    <?php
    include('../navbar.php');

    // データベース接続
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bbadb";

    $conn = null;
    $items = [];
    $genre = null;

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            throw new Exception("接続失敗: " . $conn->connect_error);
        }

        // ログインしている従業員番号を取得
        $ep_no = $_SESSION['ep_no'];

        // 従業員の店舗番号を取得
        $query = "SELECT tenpo_no FROM EMPLOYEE WHERE ep_no = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $ep_no);
        $stmt->execute();
        $stmt->bind_result($tenpo_no);
        $stmt->fetch();
        $stmt->close();

        // ジャンルが選択された場合、そのジャンルに基づいてクエリを調整
        $genre = isset($_GET['genre']) ? $_GET['genre'] : null;
        $query = "SELECT SHOHIN.name, SHOHIN.genre, STOCK_CONTROL.shohin_no, STOCK_CONTROL.stock 
                  FROM STOCK_CONTROL 
                  INNER JOIN SHOHIN ON STOCK_CONTROL.shohin_no = SHOHIN.shohin_no 
                  WHERE STOCK_CONTROL.tenpo_no = ?";
        if ($genre) {
            $query .= " AND SHOHIN.genre = ?";
        }

        $stmt = $conn->prepare($query);
        if ($genre) {
            $stmt->bind_param("is", $tenpo_no, $genre);
        } else {
            $stmt->bind_param("i", $tenpo_no);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shohin_no = $_POST['shohin_no'];
            $action = $_POST['action'];
            $amount = $_POST['amount'] ?? 0;

            switch ($action) {
                case 'increase':
                    $query = "UPDATE STOCK_CONTROL SET stock = stock + 1 WHERE shohin_no = ? AND tenpo_no = ?";
                    break;
                case 'decrease':
                    $query = "UPDATE STOCK_CONTROL SET stock = stock - 1 WHERE shohin_no = ? AND tenpo_no = ?";
                    break;
                case 'set':
                    $query = "UPDATE STOCK_CONTROL SET stock = ? WHERE shohin_no = ? AND tenpo_no = ?";
                    break;
            }

            $stmt = $conn->prepare($query);
            if ($action === 'set') {
                $stmt->bind_param("iii", $amount, $shohin_no, $tenpo_no);
            } else {
                $stmt->bind_param("ii", $shohin_no, $tenpo_no);
            }
            $stmt->execute();
            $stmt->close();

             // ページを自動更新
             echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
             exit(); // リダイレクト後は処理を終了

        }

    } catch (Exception $e) {
        echo "エラー: " . $e->getMessage();
        exit;
    }
    ?>

    <div class="container mt-5">
        <h2><i class="fas fa-boxes"></i> 在庫管理</h2>
        <form method="GET" action="" class="d-flex align-items-center mb-3">
            <div class="form-group me-3"> 
                <select id="genre" name="genre" class="form-select">
                    <option value="">全ジャンル</option>
                    <option value="肉" <?php if ($genre == '肉') echo 'selected'; ?>>1 - 肉</option>
                    <option value="魚" <?php if ($genre == '魚') echo 'selected'; ?>>2 - 魚</option>
                    <option value="飯" <?php if ($genre == '飯') echo 'selected'; ?>>3 - 飯</option>
                    <option value="飲料" <?php if ($genre == '飲料') echo 'selected'; ?>>4 - 飲料</option>
                    <option value="前菜" <?php if ($genre == '前菜') echo 'selected'; ?>>5 - 前菜</option>
                    <option value="デザート" <?php if ($genre == 'デザート') echo 'selected'; ?>>6 - デザート</option>
                    <option value="サービス" <?php if ($genre == 'サービス') echo 'selected'; ?>>7 - サービス</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> 絞り込み</button>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 30%">商品名</th>
                        <th style="width: 20%">ジャンル</th>
                        <th style="width: 15%">在庫数</th>
                        <th style="width: 35%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (empty($items)) {
                            echo "<tr><td colspan='4'>データが見つかりません。</td></tr>";
                        } else {
                            foreach ($items as $item): 
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['genre']); ?></td>
                            <td style="text-align: right;"><?php echo htmlspecialchars($item['stock']); ?>個</td>
                            <td class="action-buttons">
                                <form method="post">
                                    <input type="hidden" name="shohin_no" value="<?php echo $item['shohin_no']; ?>">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-plus"></i>増</button>
                                </form>
                                <form method="post">
                                    <input type="hidden" name="shohin_no" value="<?php echo $item['shohin_no']; ?>">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-minus"></i>減</button>
                                </form>
                                <form method="post" class="d-flex flex-row">
                                    <input type="hidden" name="shohin_no" value="<?php echo $item['shohin_no']; ?>">
                                    <input type="hidden" name="action" value="set">
                                    <input type="number" name="amount" class="form-control form-control-sm me-1 wider-input" required>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check"></i>設定</button>
                                </form>
                            </td>
                        </tr>
                    <?php 
                            endforeach; 
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <br>

    <?php
    if ($conn) {
        $conn->close();
    }
    include("../footer.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>