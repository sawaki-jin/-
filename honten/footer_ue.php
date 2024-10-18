<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フッターデザインの例</title>
    <link rel="stylesheet" href="footer.css" type="text/css">
    <!-- <link rel="stylesheet" href="footer.css" type="text/css"> -->
    <style>
    .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #f5f5f5;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 650px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- コンテンツがここに入ります -->

    <footer>
        <div class="footer-container">
            <div class="footer-section company-info"style="text-align:center;">
                <p>株式会社アトランタ</p>
                <p>〒123-4567 東京都新宿区1-2-3</p>
                <p>Email: info@example.com</p>
                <p>電話番号: 03-1329-8890</p>
            </div>
            <!--<div class="footer-section social-media">
                <a href="#"></a>
                <a href="#"></a>
                <a href="#"></a>
            </div> -->
            <div class="footer-section links" style="text-align:center;">
                <a href="index.php">ホーム</a>
                <a href="manegement/manegement.php">会社概要</a>
                <a href="#" onclick="showModal()">プライバシーポリシー</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 株式会社アトランタ. All Rights Reserved.</p>
        </div>
    </footer>

    <div id="myModal" class="modal">
        <div style = "color:#000;" class="modal-content">
        <span class="close" onclick="hideModal()">&times;</span>
            <p>1. 個人情報の収集
                            当社は、以下の場合にお客様の個人情報を収集いたします。

                            予約や問い合わせ時に提供される情報（氏名、連絡先、メールアドレスなど）
                            イベントやキャンペーンの応募時に提供される情報
                            ウェブサイトの利用状況に関する情報（クッキー、アクセスログなど）</p>

            <p>2. 個人情報の利用目的
                            当社は、収集した個人情報を以下の目的で利用いたします。

                            予約の確認やお問い合わせ対応
                            イベントやキャンペーンの案内および運営
                            サービス向上のための統計データ作成および分析
                            法律に基づく要求への対応</p>
            <p>3. 個人情報の管理
                            当社は、お客様の個人情報を適切に管理し、以下の措置を講じます。

                            不正アクセス、紛失、破壊、改ざん、漏洩の防止
                            個人情報を取り扱う従業員に対する教育および監督</p>
            <p>4. 第三者提供の禁止
                            当社は、お客様の個人情報を以下の場合を除き、第三者に提供いたしません。

                            お客様の同意がある場合
                            法令に基づく場合
                            業務委託先に対し、利用目的の達成に必要な範囲で提供する場合</p>
            <p>5. 個人情報の開示・訂正・削除
                            お客様は、当社に対して自己の個人情報の開示、訂正、削除を求める権利があります。これらの要求があった場合、当社は速やかに対応いたします。
                            </p>
            <p>6. クッキーの使用について
                            当社のウェブサイトでは、クッキーを使用してお客様の利用状況を分析し、サービス向上に役立てております。クッキーの使用を拒否することも可能ですが、その場合、当ウェブサイトの一部機能がご利用いただけない場合があります。
                            </p>
            <p>7. プライバシーポリシーの変更
                            当社は、法令の改正やサービスの変更に伴い、本プライバシーポリシーを改定することがあります。改定後のポリシーは、当社のウェブサイトに掲載いたします。
                            </p>

        </div>
    </div>

    <script>
        var modal = document.getElementById('myModal');

        function showModal() {
            modal.style.display = "block";
        }

        function hideModal() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
