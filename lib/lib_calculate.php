<?php
    require_once 'lib_math.php';

    class lib_calculate {
        private $dvds = array();
        private $dvds_type = null;
        private $error_num_types = null;
        private $post_values = array();
        private $combination_count_num = null;

        public function __construct() {
            
            $this->lib_math = new lib_math();
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

        public function set_dvds() {

            $dvds = array();
            $dvds_type = $this->get_dvds_tpye();
            foreach($dvds_type as $type) {
                $dvds[$type] = new dvd($type);
            }
            $this->dvds = $dvds;
        }

        public function get_dvd_setting($type, $key) {

            if ($type == '' || $key == '') {
                return null;
            }

            if (empty($this->dvds)) {
                $this->set_dvds();
            }
            return $this->dvds[$type]->get_setting($key);
        }

        public function get_post_value($type) {

            if (empty($this->post_values)) {
                $this->set_post_values();
            }
            return $this->post_values[$type];
        }

        public function set_post_values() {

            $post_values = array();
            $dvds_type = $this->get_dvds_tpye();
            foreach ($dvds_type as $type) {
                $post_values[$type] = $_POST[$type];
            }
            $this->post_values = $post_values;
        }

        public function set_post_values_by_param($param) {

            $post_values = array();
            $dvds_type = $this->get_dvds_tpye();
            foreach ($param as $type => $count_num) {
                $post_values[$type] = $count_num;
            }
            $this->post_values = $post_values;
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

        public function get_error_num_types() {

            if ($this->error_num_types === null) {
                $this->set_error_num_types();
            }
            return $this->error_num_types;
        }

        public function set_error_num_types() {
            
            $error_num_types = array();
            $dvds_type = $this->get_dvds_tpye();
            foreach($dvds_type as $type) {
                $count_num = $this->get_post_value($type);

                $is_native_number = $this->lib_math->is_native_number($count_num);
                $is_empty_number = $this->lib_math->is_empty_number($count_num);

                if (!$is_native_number || $is_empty_number) {
                    $error_num_types[] = $type;
                }
            }
            $this->error_num_types = $error_num_types;
        }

        public function get_dvd_info($type) {

            $result = array();
            $result['name'] = $this->get_dvd_setting($type, 'name');

            $count_num = $this->get_post_value($type);
            $result['count_num'] = $count_num;
            
            $result['more_free_count_num'] = $this->calculate_more_free_count_num($type, $count_num);
            return $result;
        }

        // 計算基礎價 (數量少於 N 個時共 M 元)
        public function calculate_base_price($type) {

            $count_num = $this->get_post_value($type);
            if (!($count_num > 0)) {
                return 0;
            }

            $result = $this->get_dvd_setting($type, 'base_price');
            return $result;
        }

        // 計算組合價 (多顏色一組)
        public function calculate_combination_price($type) {

            $count_num = $this->get_post_value($type);
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

            $count_num = $this->get_post_value($type);
            if (!($count_num > 0)) {
                return 0;
            }

            $original_price = $this->get_dvd_setting($type, 'each_price');
            $original_count_num = $this->get_original_count_num($type);

            $result = $original_price * $original_count_num;
            return $result;
        }

        // 計算價格
        public function calculate_price($type) {

            $count_num = $this->get_post_value($type);
            if (!($count_num > 0)) {
                return 0;
            }

            $result = 0;
            $result += $this->calculate_base_price($type);
            $result += $this->calculate_combination_price($type);
            $result += $this->calculate_original_price($type);
            return $result;
        }

        // 計算點數
        public function calculate_point($type) {

            $count_num = $this->get_post_value($type);
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

            $count_num = $this->get_post_value($type);
            $max_count_num = $this->get_dvd_setting($type, 'max_free_count_num');

            $result = (0 < $count_num && $count_num < $max_count_num) ? TRUE : FALSE;
            return $result;
        }

        public function calculate_more_free_count_num($type) {

            $count_num = $this->get_post_value($type);
            $max_free_count_num = $this->get_dvd_setting($type, 'max_free_count_num');
            $can_buy_more_free = $this->can_buy_more_free($type);

            $result = ($can_buy_more_free)
                            ? ($max_free_count_num - $count_num)
                            : 0;
            return $result;
        }
    }