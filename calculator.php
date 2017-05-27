<?php
    require_once 'const.php';
    require_once 'dvd.php';

    class calculator {

        private $dvds_type = null;
        private $dvds = array();
        private $post_values = array();
        private $combination_count_num = null;

        function __construct() {

            $dvds_type = $this->get_dvds_tpye();
            foreach($dvds_type as $type) {
                $this->dvds[$type] = new dvd($type);
                $this->post_values[$type] = $this->get_post_value($type);
            }
        }

        public function get_dvds_tpye() {

            if ($this->dvds_type === null) {
                $this->set_dvds_type();
            }

            return $this->dvds_type;
        }

        public function set_dvds_type() {

            $all_type_setting = _DEFAULT_SETTING::$all;
            $this->dvds_type = array_keys($all_type_setting);
        }

        public function get_dvd_setting($type, $key) {

            if ($type == '' || $key == '') {
                return null;
            }

            return $this->dvds[$type]->get_setting($key);
        }

        public function get_post_value($type) {

            $dvds_type = $this->get_dvds_tpye();
            if (!in_array($type, $dvds_type) || !array_key_exists($type, $_POST)) {
                return null;
            }

            return $_POST[$type];
        }

        public $info = array();
        public function exec() {

            if (!$this->valid()) {
                $this->show_error_message();
                return;
            }

            $this->calculate();
            $this->show();
        }

        public function valid() {

            $false_number_types = $this->get_false_number_type();
            $result = empty($false_number_types);
            return $result;
        }

        public function show_error_message() {

            $msg = array();
            $false_number_types = $this->get_false_number_type();
            foreach($false_number_types as $type) {
                $type_name = $this->get_dvd_setting($type, 'name');
                $msg[] = "{$type_name}數量請輸入非負整數!";
            }
            echo join('<br>', $msg);
        }

        public function calculate() {

            // init
            $info = array(
                'types_info' => array(),
                'total_price' => 0,
                'total_point' => 0
            );

            $dvds_type = $this->get_dvds_tpye();
            $total_price = $total_point = 0;
            foreach ($dvds_type as $type) {
                $count_num = $this->post_values[$type];
                $total_price += $this->calculate_price($type, $count_num);
                $total_point += $this->calculate_point($type, $count_num);

                $info['types_info'][$type] = $this->get_dvd_info($type);
            }

            $info['total_price'] = $total_price;
            $info['total_point'] = $total_point;
            $this->info = $info;
        }

        public function show() {

            $msg = array();
            
            $info = $this->info;
            $types_info = $info['types_info'];
            foreach ($types_info as $type => $type_info) {
                $type_name = $type_info['name'];
                $count_num = $type_info['count_num'];
                $more_free_count_num = $type_info['more_free_count_num'];

                $msg["{$type}_info"] = "您購買{$type_name} {$count_num} 片";
                if ($more_free_count_num > 0) {
                    $msg["{$type}_info"] .= "，可以再多拿 " . strval($more_free_count_num)  . " 片，價格不變唷!";
                }
            }

            $total_price = $info['total_price'];
            $total_point = $info['total_point'];

            $msg['total_price'] = "總金額 {$total_price} 元";
            $msg['total_point'] = "此次消費積點為 {$total_point} 點";
            if ($total_point >= _GIFT_NEEDED::$point) {
                $msg['total_point'] .= "，恭喜您獲得神秘小禮物一份!";
            }
            echo join('<br>', $msg);
        }

        private function is_native_number($num_str) {

            $result = (preg_match('/^\d+$/', $num_str)) ? TRUE : FALSE;
            return $result;
        }

        private function is_empty_number($num_str) {

            $result = ($num_str === '') ? TRUE : FALSE;
            return $result;
        }

        public function get_false_number_type() {

            $dvds_type = $this->get_dvds_tpye();
            $result = array();
            foreach($dvds_type as $type) {
                $count_num = $this->post_values[$type];

                $is_native_number = $this->is_native_number($count_num);
                $is_empty_number = $this->is_empty_number($count_num);

                if (!$is_native_number || $is_empty_number) {
                    $result[] = $type;
                }
            }
            return $result;
        }

        public function get_original_count_num($type) {

            $count_num = $this->post_values[$type];
            $max_free_count_num = $this->get_dvd_setting($type, 'max_free_count_num');
            $combination_count_num = $this->get_combination_count_num();

            $combination_type = _COMBINATION_SETTING::$type;
            $result = (!in_array($type, $combination_type))
                            ? $count_num - $max_free_count_num
                            : ($count_num - $max_free_count_num - $combination_count_num);
            return $result;
        }

        public function get_combination_count_num() {

            if ($this->combination_count_num === null) {
                $this->set_combination_count_num();
            }
            return $this->combination_count_num;
        }

        public function set_combination_count_num() {

            $dvds_type = $this->get_dvds_tpye();
            $outer_count_nums = array();
            foreach($dvds_type as $type) {
                $count_num = $this->post_values[$type];
                $max_free_count_num = $this->get_dvd_setting($type, 'max_free_count_num');
                $outer_count_num = ($count_num - $max_free_count_num);

                $outer_count_nums[$type] = ($outer_count_num > 0) ? $outer_count_num : 0;
            }

            $combination_type = _COMBINATION_SETTING::$type;
            $count_nums = array();
            foreach($outer_count_nums as $type => $count_num) {
                if (!in_array($type, $combination_type)) {
                    continue;
                }

                $count_nums[$type] = $count_num;
            }

            $combination_count_num = 0;
            if (!empty($count_nums)) {
                $combination_count_num = min($count_nums);
            }
            $this->combination_count_num = $combination_count_num;
        }

        // 計算基礎價 (數量少於 N 個時共 M 元)
        public function calculate_base_price($type) {

            $count_num = $this->post_values[$type];
            if (!($count_num > 0)) {
                return 0;
            }

            $result = $this->get_dvd_setting($type, 'base_price');
            return $result;
        }

        // 計算組合價 (多顏色一組)
        public function calculate_combination_price($type) {

            $count_num = $this->post_values[$type];
            if (!($count_num > 0)) {
                return 0;
            }

            $combination_price = $this->get_dvd_setting($type, 'combination_price');
            $combination_count_num = $this->get_combination_count_num();
            
            $result = $combination_price * $combination_count_num;
            return $result;
        }

        // 計算原價
        public function calculate_original_price($type) {

            $count_num = $this->post_values[$type];
            if (!($count_num > 0)) {
                return 0;
            }

            $original_price = $this->get_dvd_setting($type, 'each_price');
            $original_count_num = $this->get_original_count_num($type);

            $result = $original_price * $original_count_num;
            return $result;
        }

        public function calculate_price($type) {

            $count_num = $this->post_values[$type];
            if (!($count_num > 0)) {
                return 0;
            }

            $result = 0;
            $result += $this->calculate_base_price($type);
            $result += $this->calculate_combination_price($type);
            $result += $this->calculate_original_price($type);
            return $result;
        }

        public function calculate_point($type) {

            $count_num = $this->post_values[$type];
            if (!($count_num > 0)) {
                return 0;
            }

            $max_point = $this->get_dvd_setting($type, 'max_point');
            $each_point = $this->get_dvd_setting($type, 'each_point');

            $point = $count_num * $each_point;
            $result = ($point > $max_point) ? $max_point : $point;
            return $result;
        }

        private function can_buy_more_free($type) {

            $count_num = $this->post_values[$type];
            $max_count_num = $this->get_dvd_setting($type, 'max_free_count_num');

            $result = (0 < $count_num && $count_num < $max_count_num) ? TRUE : FALSE;
            return $result;
        }

        public function calculate_more_free_count_num($type) {

            $count_num = $this->post_values[$type];
            $max_free_count_num = $this->get_dvd_setting($type, 'max_free_count_num');
            $can_buy_more_free = $this->can_buy_more_free($type);

            $result = ($can_buy_more_free)
                            ? ($max_free_count_num - $count_num)
                            : 0;
            return $result;
        }

        public function get_dvd_info($type) {

            $result = array();
            $result['name'] = $this->get_dvd_setting($type, 'name');

            $count_num = $this->post_values[$type];
            $result['count_num'] = $count_num;
            
            $result['more_free_count_num'] = $this->calculate_more_free_count_num($type, $count_num);
            return $result;
        }
    }