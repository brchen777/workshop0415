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
<form action="dvd.php" method="post">
    <div class="movie_num">紅標 <input type="text" name="red"> 片</div>
    <div class="movie_num">綠標 <input type="text" name="green"> 片</div>
    <div class="movie_num">藍標 <input type="text" name="blue"> 片</div>
    <div><button type="submit">計算</button></div>
</form>
<br>
<?php
    class calculator {
        private $dvd_type = array('red', 'green', 'blue');

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

                    foreach ($this->dvd_type as $type) {
                        $count_num = $_POST[$type];
                        if ($count_num === '') {
                            continue;
                        }

                        $type_name = $this->get_type_name($type);
                        if ($this->isNativeNumberString($count_num)) {
                            echo "{$type_name}數量請輸入非負整數! <br>";
                            continue;
                        }

                        $total_price += $this->calculate_price($type, $count_num);
                        $total_point += $this->calculate_point($type, $count_num);

                        echo $this->output_info($type, $count_num);
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

        // dvd 標籤名字
        private function get_type_name($type) {

            $name = '';
            switch($type) {
                case 'red':
                    $name = '紅標';
                break;

                case 'green':
                    $name = '綠標';
                break;

                case 'blue':
                    $name = '藍標';
                break;
            }

            return $name;
        }

        // 每一種 dvd 的價格 (元/每片)
        private function get_each_price($type) {

            switch($type) {
                case 'red':
                    $price = 40;
                break;

                case 'green':
                    $price = 12;
                break;

                case 'blue':
                    $price = 10;
                break;

                default:
                    $price = 0;
                break;
            }

            return $price;
        }

        // 每一種 dvd 組合價的價格 (元)
        private function get_free_total_price($type) {

            switch($type) {
                case 'red':
                    $price = 60;
                break;

                case 'green':
                    $price = 30;
                break;

                case 'blue':
                    $price = 25;
                break;

                default:
                    $price = 0;
                break;
            }

            return $price;
        }

        // 每一種 dvd 的集點 (點/每片)
        private function get_each_point($type) {

            switch($type) {
                case 'red':
                    $point = 3;
                break;

                case 'green':
                    $point = 1;
                break;

                default:
                case 'blue':
                    $point = 0;
                break;
            }

            return $point;
        }

        // 每一種 dvd 集點上限 (點)
        private function get_max_point($type) {

            switch($type) {
                case 'red':
                    $point = 15;
                break;

                case 'green':
                    $point = 8;
                break;

                default:
                case 'blue':
                    $point = 0;
                break;
            }

            return $point;
        }

        // 每一種 dvd 可購買組合價的上限 (片)
        private function get_max_free_count_num($type) {

            switch($type) {
                case 'red':
                    $count_num = 2;
                break;

                case 'green':
                case 'blue':
                    $count_num = 3;
                break;

                default:
                    $count_num = 0;
                break;
            }

            return $count_num;
        }

        private function isNativeNumberString($numString) {

            return (preg_match('/^\d+$/', $numString)) ? FALSE : TRUE;
        }

        private function calculate_price($type, $count_num) {

            if (!($count_num > 0)) {
                return 0;
            }

            $count_num = (int)$count_num;
            $each_price = $this->get_each_price($type);
            $free_total_price = $this->get_free_total_price($type);
            $max_free_count_num = $this->get_max_free_count_num($type);

            $price = ($count_num <= $max_free_count_num) 
                            ? $free_total_price 
                            : $free_total_price + ($count_num - $max_free_count_num) * $each_price;
            return $price;
        }

        private function calculate_point($type, $count_num) {

            $max_point = $this->get_max_point($type);
            $each_point = $this->get_each_point($type);

            $point = $count_num * $each_point;
            $point = ($point > $max_point) ? $max_point : $point;
            return $point;
        }

        private function can_buy_more_free($type, $count_num) {

            $min_count_num = 0;
            $max_count_num = $this->get_max_free_count_num($type);

            $result = ($count_num > $min_count_num && $count_num < $max_count_num) ? TRUE : FALSE;
            return $result;
        }

        private function output_info($type, $count_num) {

            $type_name = $this->get_type_name($type);
            $max_free_count_num = $this->get_max_free_count_num($type);

            $msg = "您購買" . $type_name . $count_num . "片";
            if ($this->can_buy_more_free($type, $count_num)) {
                $msg .= "，可以再多拿" . strval($max_free_count_num - $count_num)  . "片，價格不變唷!";
            }
            $msg .= "<br>";
            
            return $msg;
        }
    }

    $calculator = new calculator();
    $calculator->calculate();
?>
</body>
</html>