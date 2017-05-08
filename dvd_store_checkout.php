<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>結帳</title>
    <style>
        .movie_num{
            padding-bottom: 5px;
        }

        .title{
            font-size: 18px;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="title">DVD 結帳系統</div>
<form action="dvd_store_checkout.php" method="post">
    <div class="movie_num">紅標 <input type="text" name="red"> 片</div>
    <div class="movie_num">綠標 <input type="text" name="green"> 片</div>
    <div class="movie_num">藍標 <input type="text" name="blue"> 片</div>
    <div><button type="submit">計算</button></div>
</form>
<br>
<?php
    class calculator {
        public function calculate() {
            if (isset($_POST['red'])) {

                // 全部沒輸入
                if ($_POST['red'] == '' && $_POST['green'] == '' && $_POST['blue'] == '') {
                    echo "請輸入 dvd 數量! <br>";
                }
                else {
                    // 有輸入任一值
                    $total_price = 0;
                    $total_point = 0;
                    if ($_POST['red'] != '') {
                        // 紅標有值
                        if ($this->isNativeNumberString($_POST['red'])) {
                            echo "紅標數量請輸入非負整數! <br>";
                        }
                        else {
                            $red_count_num = intval($_POST['red']);
                                                    
                            $red_price = $this->calculate_red_price($red_count_num);
                            $total_price += $red_price;

                            $red_point = $this->calculate_red_point();
                            $total_point += $red_point;

                            echo $this->output_red_info($red_count_num);
                        }
                    }

                    if ($_POST['green'] != '') {
                        if (preg_match('/^\d+$/', $_POST['green'])) {
                            echo "您購買綠標" . $_POST['green'] . "片";
                            if (intval($_POST['green']) > 0) {
                                $total_price = $this->calculate_green_price($total_price);
                            }
                            echo "<br>";

                            $green_point = $this->calculate_green_point();

                            $total_point += $green_point;
                        }
                        else {
                            echo "綠標數量請輸入非負整數! <br>";
                        }
                    }

                    if ($_POST['blue'] != '') {
                        if (preg_match('/^\d+$/', $_POST['blue'])) {
                            echo "您購買藍標" . $_POST['blue'] . "片";
                            if (intval($_POST['blue']) > 0) {
                                $total_price = $this->calculate_blue_price($total_price);
                            }
                            echo "<br>";
                        }
                        else {
                            echo "藍標數量請輸入非負整數! <br>";
                        }
                    }

                    echo "總金額 {$total_price} 元 <br>";
                    echo "<br>";
                    echo "此次消費積點為 {$total_point} 點";
                    if ($total_point >= 20) {
                        echo "，恭喜您獲得神秘小禮物一份! <br>";
                    }
                }
            }
        }

        private function isNativeNumberString($numString) {

            return (preg_match('/^\d+$/', $numString) === FALSE);
        }

        private function output_red_info($red_count_num) {

            $msg =  "您購買紅標" . $_POST['red'] . "片";
            if ($this->red_can_buy_more_free($red_count_num)) {
                $msg .= "，可以再多拿" . strval(2 - $red_count_num)  . "片，價格不變唷!";
            }
            $msg .= "<br>";

            return $msg;
        }

        private function red_can_buy_more_free($red_count_num) {

            return ($red_count_num > 0 && $red_count_num < 2);
        }

        private function calculate_red_price($red_count_num) {

            $price = 0;
            if ($red_count_num > 0) {
                $price = ($red_count_num <= 2)
                            ? 60
                            : 60 + ($red_count_num - 2) * 40;
            }

            return $price;
        }

        private function calculate_red_point() {

            $red_point = intval($_POST['red']) * 3;
            if ($red_point > 15) {
                $red_point = 15;
            }

            return $red_point;
        }

        private function calculate_green_price($price) {

            $green_max = 3;
            if (intval($_POST['green']) <= $green_max) {
                $price += 30;
                if (intval($_POST['green']) != $green_max) {
                    echo "，可以再多拿" . strval($green_max - intval($_POST['green'])) . "片，價格不變唷!";
                }
            }
            else {
                $price += 30 + (intval($_POST['green']) - $green_max) * 12;
            }

            return $price;
        }

        private function calculate_green_point() {

            $green_point = intval($_POST['green']) * 1;
            if ($green_point > 8) {
                $green_point = 8;
            }

            return $green_point;
        }

        private function calculate_blue_price($price) {

            $blue_max = 3;
            if (intval($_POST['blue']) <= $blue_max) {
                $price += 25;
                if (intval($_POST['blue']) != $blue_max) {
                    echo "，可以再多拿" . strval($blue_max - intval($_POST['blue'])) . "片，價格不變唷!";
                }
            }
            else {
                $price += 25 + (intval($_POST['blue']) - $blue_max) * 10;
            }

            return $price;
        }
    }

    $calculator = new calculator();
    $calculator->calculate();
?>
</body>
</html>
<?php
