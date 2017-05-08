<?php
    require_once 'index.php';

    // name:       dvd 標籤名字
    // base_price: 每一種 dvd 組合價的價格 (元)
    // each_price: 每一種 dvd 的價格 (元/每片)
    // each_point: 每一種 dvd 的集點 (點/每片)
    // max_point:  每一種 dvd 的集點上限 (點)
    // max_free_count_num: 每一種 dvd 可購買組合價的上限 (片)
    class _DEFAULT_SETTING {

        const all = array(
            'red' => array(
                'name'       => '紅標',
                'base_price' => 60,
                'each_price' => 40,
                'each_point' => 3,
                'max_point'  => 15,
                'max_free_count_num' => 2
            ),
            'green' => array(
                'name'       => '綠標',
                'base_price' => 30,
                'each_price' => 12,
                'each_point' => 1,
                'max_point'  => 8,
                'max_free_count_num' => 3
            ),
            'blue' => array(
                'name'       => '藍標',
                'base_price' => 25,
                'each_price' => 10,
                'each_point' => 0,
                'max_point'  => 0,
                'max_free_count_num' => 3
            )
        );
    }

    class dvd {

        private static $all_setting;
        private $setting;

        function __construct($type) {
            
            $all_setting = _DEFAULT_SETTING::all;
            $setting_param = $all_setting[$type];
            $this->set_setting($type, $setting_param);
        }

        public function get_setting($key) {

            return $this->setting[$key];
        }

        public function set_setting($type, $param=array()) {

            $setting = array();
            foreach($param as $key => $val) {
                $setting[$key] = $val;
            }
            $this->setting = $setting;
        }
    }

    class calculator {

        private $dvd_type = array('red', 'green', 'blue');
        private $dvd = array();
        private $post_value = array();

        function __construct() {

            foreach($this->dvd_type as $type) {
                $this->dvd[$type] = new dvd($type);
                $this->post_value[$type] = $this->get_post_value($type);
            }
        }

        public function get_dvd_setting($type, $key) {

            return $this->dvd[$type]->get_setting($key);
        }

        public function get_post_value($type) {

            return $_POST[$type];
        }

        public function calculate() {

            $get_false_number_type = $this->get_false_number_type();
            if (!empty($get_false_number_type)) {
                echo $this->output_error_message($get_false_number_type);
                return;
            }

            $total_price = 0;
            $total_point = 0;

            foreach ($this->dvd_type as $type) {
                $count_num = $this->post_value[$type];

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

        private function is_native_number($num_str) {

            $result = (preg_match('/^\d+$/', $num_str)) ? TRUE : FALSE;
            return $result;
        }

        private function is_empty_number($num_str) {

            $result = ($num_str === '') ? TRUE : FALSE;
            return $result;
        }

        private function get_false_number_type() {

            $result = array();
            foreach($this->dvd_type as $type) {
                $count_num = $this->post_value[$type];

                $is_native_number = $this->is_native_number($count_num);
                $is_empty_number = $this->is_empty_number($count_num);

                if (!$is_native_number || $is_empty_number) {
                    $result[] = $type;
                }
            }
            return $result;
        }

        private function output_error_message($types=array()) {

            if (empty($types)) {
                return '';
            }

            $msg = array();
            foreach($types as $type) {
                $type_name = $this->get_dvd_setting($type, 'name');
                $msg[] = "{$type_name} 的 dvd 數量請輸入非負整數! <br>";
            }
            return join('', $msg);
        }

        private function calculate_price($type, $count_num) {

            if (!($count_num > 0)) {
                return 0;
            }

            $count_num = (int)$count_num;
            $each_price = $this->get_dvd_setting($type, 'each_price');
            $base_price = $this->get_dvd_setting($type, 'base_price');
            $max_free_count_num = $this->get_dvd_setting($type, 'max_free_count_num');

            $price = ($count_num <= $max_free_count_num) 
                            ? $base_price 
                            : $base_price + ($count_num - $max_free_count_num) * $each_price;
            return $price;
        }

        private function calculate_point($type, $count_num) {

            if (!($count_num > 0)) {
                return 0;
            }

            $max_point = $this->get_dvd_setting($type, 'max_point');
            $each_point = $this->get_dvd_setting($type, 'each_point');

            $point = $count_num * $each_point;
            $point = ($point > $max_point) ? $max_point : $point;
            return $point;
        }

        private function can_buy_more_free($type, $count_num) {

            $max_count_num = $this->get_dvd_setting($type, 'max_free_count_num');

            $result = (0 < $count_num && $count_num < $max_count_num) ? TRUE : FALSE;
            return $result;
        }

        private function output_info($type, $count_num) {

            $type_name = $this->get_dvd_setting($type, 'name');
            $max_free_count_num = $this->get_dvd_setting($type, 'max_free_count_num');

            $msg = "您購買{$type_name} {$count_num} 片";
            if ($this->can_buy_more_free($type, $count_num)) {
                $msg .= "，可以再多拿 " . strval($max_free_count_num - $count_num)  . " 片，價格不變唷!";
            }
            $msg .= "<br>";
            return $msg;
        }
    }

    function main() {
        echo "<br>";
        $calculator = new calculator();
        $calculator->calculate();
    }
    main();