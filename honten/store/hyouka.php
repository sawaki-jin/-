<?php
session_start();
$datasets = array();

$erromsg2 = "データなし";
//データの受け取り
if (isset($_POST['tenpo_no'])) {
    $tenpo_no = $_POST['tenpo_no'];
}

//12ヶ月分の配列作成
for ($i = 1; $i <= 12; $i++) {
    $date[$i] = 0;
}

$dsn = 'mysql:host=localhost;dbname=bbadb;charset=utf8';
$user = 'root';
$password = '';

try {
    $db = new PDO($dsn, $user, $password);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $stmt = $db->prepare("
            SELECT MIN(salesdate) AS mindate FROM exp_control
            WHERE tenpo_no = :tenpo_no
        ");

    $stmt->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_INT);

    $stmt->execute();


    $stmt0 = $db->prepare("
            SELECT MAX(salesdate) AS maxdate FROM exp_control
            WHERE tenpo_no = :tenpo_no
        ");

    $stmt0->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_INT);

    $stmt0->execute();

    while ($row = $stmt->fetch()) {
        $minyear = (int)substr($row['mindate'], 0, 4);
    }
    while ($row = $stmt0->fetch()) {
        $maxyear = (int)substr($row['maxdate'], 0, 4);
    }
    $diff = $maxyear - $minyear;
    $year = $maxyear;

    for ($yr = $minyear; $yr <= $maxyear; $yr++) {
        for ($i = 1; $i <= 12; $i++) {
            $data[$yr][$i] = 0;
            $putdata[$yr][$i] = 0;
        }
    }

    for ($y = 0; $y <= $diff; $y++) {
        //あいまい検索のデータ
        for ($i = 1; $i <= 12; $i++) {
            //1月～9月
            if ($i <= 9) {
                $tuki = $year . "-0" . $i . "%";
            }
            //10月～12月
            else {
                $tuki = $year . "-" . $i . "%";
            }


            //1か月分のデータ取得
            $stmt1 = $db->prepare("
                    SELECT * FROM exp_control
                    WHERE tenpo_no = :tenpo_no
                    AND salesdate LIKE :tuki
                ");

            $stmt1->bindParam(':tenpo_no', $tenpo_no, PDO::PARAM_INT);
            $stmt1->bindParam(':tuki', $tuki, PDO::PARAM_STR);
            $stmt1->execute();

            //ひと月分のデータ
            while ($row = $stmt1->fetch()) {
                $data[$year][$i] += $row['uriage'];
            };
            $putdata[$year][$i] = $data[$year][$i];
        }

        $year--;
    }

    for ($yr = $maxyear; $yr >= $minyear; $yr--) {
        $datasets[$yr] = array();
        for ($i = 1; $i <= 12; $i++) {
            $datasets[$yr][] = $putdata[$yr][$i];
        }
    }

    $json_datasets = json_encode($datasets);


    $stmt2 = $db->prepare("
            SELECT * FROM tenpo
        ");

    $stmt2->execute();

    while ($row = $stmt2->fetch()) {
        if ($row['tenpo_no'] == $tenpo_no) { //店舗があるかチェック
            $tenpo_name = $row['tenpo_name'];
            $erromsg2 = NULL;
            break;
        }
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
    <title>評価</title>
    <!-- Bootstrap CSSの読み込み -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!-- Googleフォントデフォルト -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <!-- Googleフォントデフォルト -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesomeの読み込み -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> 
    <style>

    .container {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 10px; /* padding の値を小さくする */
        margin-top: 30px;
    }

        h1 {
            color: #343a40; /* ダークグレーのテキスト */
        }

        canvas {
            background-color: #fff;
            border: 1px solid #ced4da; /* ライトグレーの境界線 */
            border-radius: 5px;
        }
    </style>
</head>
<body>
<header>
    <!-- Bootstrap JavaScriptの読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <!-- navbar.phpここから -->
    <?php include('../navbar.php') ?>
    <!-- navbar.phpここまで -->
</header>
<?php
if (isset($erromsg2)) {
    echo "<h1>" . $erromsg2 . "</h1>";
}
?>

<?php if (empty($erromsg2)): ?>
    <div class="container" style="max-width: 70%; max-height:70%;">
        <h1><i class="fas fa-star"></i> 評価 <?php echo $tenpo_name; ?></h1>
        <select id="yearSelector" class="form-select form-select-sm" style="width: auto;">
            <?php for ($yr = $maxyear; $yr >= $minyear; $yr--): ?>
                <option value="<?= $yr ?>"><?= $yr ?>年</option>
            <?php endfor; ?>
        </select>
        <canvas id="myChart"></canvas>
        <input type="button" class="btn btn-secondary mt-3" onclick="location.href='list.php'" value="一覧へ戻る">
    </div>
<?php endif ?>
<br>
<script>
    // データセット
    const datasets = <?php echo $json_datasets; ?>;
    console.log(datasets);

    // 基準線を描画するプラグイン
    const baselinePlugin = {
        id: 'baselinePlugin',
        afterDatasetsDraw: function (chart, args, options) {
            const {ctx, scales: {y}} = chart;
            const baselineValue = options.value;
            const label = options.label || '';
            const padding = options.padding || 10;

            // Y座標の位置を取得
            const yPos = y.getPixelForValue(baselineValue);

            ctx.save();
            ctx.beginPath();
            ctx.moveTo(chart.chartArea.left, yPos);
            ctx.lineTo(chart.chartArea.right, yPos);
            ctx.strokeStyle = options.color || 'red';
            ctx.lineWidth = options.lineWidth || 2;
            ctx.stroke();

            // ラベルの描画
            ctx.font = '16px Arial';
            ctx.fillStyle = options.color || 'red';
            ctx.fillText(label, chart.chartArea.left + padding, yPos - padding);
            ctx.restore();
        }
    };

    // プラグインをChart.jsに登録
    Chart.register(baselinePlugin);

    // 最新の年度を選択
    const years = Object.keys(datasets);
    const latestYear = Math.max(...years.map(year => parseInt(year, 10))).toString();

    //最新の年度をselectboxに入れる
    document.getElementById('yearSelector').value = latestYear;

    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar', // グラフのタイプ (bar, line, pie, etc.)
        data: {
            labels: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
            datasets: [{
                label: '売上',
                data: datasets[latestYear],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                baselinePlugin: {
                    value: 500000, // 基準線の値
                    label: '目標値', // 基準線のラベル
                    color: 'red', // 基準線の色
                    lineWidth: 1, // 基準線の太さ
                    padding: 10 // 基準線とラベルの間のスペース
                },
                title: {
                    display: true,
                    /*text: `${latestYear}`,*/
                    font: {
                        size: 24
                    }
                }
            }
        }
    });

    //selectboxで選択された年度に更新
    document.getElementById('yearSelector').addEventListener('change', function () {
        const selectedYear = this.value;
        myChart.data.datasets[0].data = datasets[selectedYear];
        myChart.options.plugins.title.text = selectedYear + '年';
        myChart.update();
    });
</script>

</body>
</html>