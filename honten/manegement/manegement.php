<?php include '../login_check.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>月影庵</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/button.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .main-content {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .heading {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .section-title {
            color: #34495e;
            margin-top: 25px;
        }

        .content {
            margin-bottom: 15px;
        }

        .slogan {
            font-size: 1.2em;
            font-style: italic;
            color: #3498db;
            text-align: center;
            margin: 30px 0;
        }

        ul.content {
            padding-left: 20px;
        }

        ul.content li {
            margin-bottom: 10px;
        }

        .feature-list {
            list-style-type: none;
            padding: 0;
        }

        .feature-list li {
            background-color: #ecf0f1;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 5px;
        }

        .feature-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            height: 100%;
        }

        /* タブのスタイル */
        .nav-tabs {
            border-bottom: none;
            margin: 10px 0;
            padding: 0 50px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #333;
            font-weight: bold;
            padding: 10px 20px;
            border-bottom: 2px solid transparent;
        }

        .nav-tabs .nav-link.active {
            color: #3498db;
            background-color: transparent;
            border-bottom: 2px solid #3498db;
        }

        /* タブ内のボタンのスタイル */
        .nav-tabs .nav-item .nav-link {
            width: 200px;
            text-align: center;
        }

        .tab-content {
            padding: 30px 20px;
            background-color: #fff;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .feature-box {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

    <?php include('../navbar.php'); ?>

    <div class="container zentai">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">経営方針</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu" type="button" role="tab" aria-controls="menu" aria-selected="false">他社比較</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">

            <!-- 経営方針タブ -->
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="container main-content">
                    <h1 class="heading">経営理念</h1>
                    <p class="content">「お客様を笑顔に」</p>

                    <h2 class="heading">経営方針</h2>

                    <h3 class="section-title">心温まるおもてなし</h3>
                    <p class="content">
                        お客様一人ひとりに寄り添ったサービスを提供し、居心地の良い空間を作ります。笑顔と感謝の気持ちを忘れず、常にお客様を第一に考えた対応を心掛けます。
                    </p>

                    <h3 class="section-title">高品質な料理と飲み物</h3>
                    <p class="content">
                        新鮮な食材を使用し、料理のクオリティを常に高く保ちます。多彩なメニューを取り揃え、お客様の多様なニーズに応えます。
                    </p>

                    <h3 class="section-title">地域とのつながり</h3>
                    <p class="content">
                        地元の食材や文化を積極的に取り入れ、地域との絆を深めます。地域イベントやキャンペーンを通じて、地域社会に貢献します。
                    </p>

                    <h3 class="section-title">スタッフの成長とチームワーク</h3>
                    <p class="content">
                        スタッフ一人ひとりの成長を支援し、働きがいのある職場を提供します。チームワークを大切にし、皆で協力してお客様をお迎えします。
                    </p>

                    <h3 class="section-title">持続可能な経営</h3>
                    <p class="content">
                        環境に配慮した経営を実践し、持続可能な社会の実現に貢献します。長期的な視点での成長を目指し、安定した経営基盤を築きます。
                    </p>

                    <div class="slogan">「アトランタで、笑顔と幸せを一緒に。」</div>

                    <h2 class="heading">行動指針</h2>
                    <ul class="content">
                        <li>笑顔と挨拶でお客様をお迎えします。</li>
                        <li>清潔で快適な店内環境を維持します。</li>
                        <li>お客様の声に耳を傾け、サービス向上に努めます。</li>
                        <li>チーム一丸となって目標達成を目指します。</li>
                        <li>誠実で透明な経営を行います。</li>
                    </ul>

                    <p class="content">
                        居酒屋「アトランタ」は、お客様の笑顔を何よりも大切にし、心からくつろげるひとときを提供いたします。お客様の笑顔が私たちの喜びであり、成長の原動力です。これからも皆様のご期待に応えられるよう、努力してまいります。
                    </p>
                </div>
            </div>

            <!-- 他社比較タブ -->
            <div class="tab-pane fade" id="menu" role="tabpanel" aria-labelledby="menu-tab">
                <div class="container main-content">
                    <h1 class="heading">アトランタの強みと特徴</h1>

                    <ul class="feature-list">
                        <li class="content">
                            <strong>心温まるおもてなし:</strong> お客様一人ひとりに寄り添ったサービス、スタッフ全員が笑顔でお迎え、アットホームな雰囲気。
                        </li>
                        <li class="content">
                            <strong>高品質な料理と飲み物:</strong> 新鮮な食材を使用し、多彩なメニューとオリジナル料理、季節ごとの特別メニュー。
                        </li>
                        <li class="content">
                            <strong>地域とのつながり:</strong> スタッフ教育と成長支援、働きがいのある職場環境、チームワークの重視。
                        </li>
                        <li class="content">
                            <strong>持続可能な経営:</strong> 環境に配慮した取り組み、長期的な視点での成長戦略、安定した経営基盤。
                        </li>
                    </ul>

                    <h1 class="heading">他社居酒屋の特徴</h1>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="feature-box">
                                <h2 class="section-title">チェーン店A</h2>
                                <ul class="content">
                                    <li>大手チェーンの安定感</li>
                                    <li>統一されたマニュアルとサービス</li>
                                    <li>豊富な店舗数でアクセス良好</li>
                                    <li>メニューの標準化で一定の品質</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-box">
                                <h2 class="section-title">チェーン店B</h2>
                                <ul class="content">
                                    <li>低価格メニューの充実</li>
                                    <li>ファミリー向けの雰囲気</li>
                                    <li>定期的なプロモーションや割引</li>
                                    <li>マスコットキャラクターやブランドの強み</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-box">
                                <h2 class="section-title">個人経営の居酒屋C</h2>
                                <ul class="content">
                                    <li>独自のコンセプトやテーマ</li>
                                    <li>店主の個性が光るおもてなし</li>
                                    <li>限定メニューや創作料理</li>
                                    <li>常連客との深い信頼関係</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <br>

                    <h1 class="heading">アトランタの差別化ポイント</h1>
                    <p class="content">おもてなしの質：アトランタは、チェーン店にはない個別対応と、個人経営店にも負けない温かいサービスを提供します。</p>
                    <p class="content">料理のクオリティ：大手チェーンの安定感と、個人経営店の独自性を兼ね備えた多彩で高品質な料理を提供します。</p>
                    <p class="content">スタッフの成長支援：スタッフの教育と働きがいを重視し、全員が一体となってお客様をお迎えするチームワークは、他社と比べて一歩抜きん出ています。</p>
                    <p class="content">持続可能な経営：環境への配慮と持続可能な経営方針により、長期的な成長を目指す姿勢は、現代社会において重要なポイントです。</p>

                    <h4>
                        居酒屋「アトランタ」は、お客様の笑顔を何よりも大切にし、心からくつろげるひとときを提供することで、他社との差別化を図ります。お客様の声に耳を傾け、常にサービス向上に努めることで、居心地の良い空間を作り出しています。
                    </h4>
                </div>
            </div>

        </div>
    </div>

    <!-- フッター -->
    <?php include "../footer.php";?>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>
</html>