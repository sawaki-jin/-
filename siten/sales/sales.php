<?php include '../login_check.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>月影庵</title>
        <link rel="icon" href="images/favicon.jpg" sizes="any">
        <!-- Bootstrap CSSの読み込み -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="../../css/style.css">
        <link rel="stylesheet" href="../../css/button.css">
    </head>
    <body>
        <?php include('../navbar.php');?>
        
        <!-- ボタン -->
        <div class="container text-center">
            <div class="row justify-content-center align-items-center" style="height: 80vh;"> <!-- 高さを80vhに設定 -->
                <div class="col-auto">
                    <a href="product_list.php" class="btn--circle">
                        <i class="far fa-envelope"></i>
                        <br>商品一覧
                        <i class="fas fa-angle-down fa-position-bottom"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <a href="salse_management.php" class="btn--circle">
                        <i class="far fa-envelope"></i>
                        <br>在庫管理
                        <i class="fas fa-angle-down fa-position-bottom"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- フッター -->
        <!-- <?php include "../footer.php";?> -->

        <!-- Bootstrap JavaScriptの読み込み -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
</html>
