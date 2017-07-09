<?php
require_once __DIR__ . '/../const.php';
require_once __DIR__ . '/../dvd.php';
require_once 'lib_math.php';

class lib_calculator {

    private $dvds = array();
    private $dvds_type = null;
    private $error_num_types = null;
    private $post_values = array();
    private $combination_count_num = null;

    public function __construct() {

        $this->lib_math = new lib_math();
    }

    public function purge() {

        $this->dvds = array();
        $this->dvds_type = null;
        $this->error_num_types = null;
        $this->post_values = array();
        $this->combination_count_num = null;
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

    public function get_post_value($type, $convertToInt=TRUE) {

        if (empty($this->post_values)) {
            $this->set_post_values();
        }

        $result = $this->post_values[$type];
        $result = ($convertToInt) ? (int)$result : $result;
        return $result;
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

        $this->purge();

        $post_values = array();
        $dvds_type = $this->get_dvds_tpye();
        foreach ($param as $type => $count_num) {
            $post_values[$type] = $count_num;
        }
        $this->post_values = $post_values;
    }

    public function get_original_count_num($type) {

        $count_num = $this->get_post_value($type);
        $max_free_count_num = $this->get_dvd_setting($type, 'max_free_count_num');
        $combination_count_num = $this->get_combination_count_num();

        $combination_type = _COMBINATION_SETTING::$type;
        $result = (!in_array($type, $combination_type))
                        ? $count_num - $max_free_count_num
                        : ($count_num - $max_free_count_num - $combination_count_num);
        $result = ($result > 0) ? $result : 0;
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
            $count_num = $this->get_post_value($type);
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
            $count_num = $this->get_post_value($type, FALSE);
            $is_native_number = $this->lib_math->is_native_number($count_num);
            $is_empty_number = $this->lib_math->is_empty_number($count_num);

            if (!$is_native_number || $is_empty_number) {
                $error_num_types[] = $type;
            }
        }
        $this->error_num_types = $error_num_types;
    }

    // 可以以基礎價再購買數量 (數量少於 N 個時共 M 元)
    public function get_more_free_count_num($type) {

        $count_num = $this->get_post_value($type);
        $max_free_count_num = $this->get_dvd_setting($type, 'max_free_count_num');
        $can_buy_more_free = (0 < $count_num && $count_num < $max_free_count_num) ? TRUE : FALSE;

        $result = ($can_buy_more_free)
                        ? ($max_free_count_num - $count_num)
                        : 0;
        return $result;
    }

    // 優惠價 (數量少於 N 個時共 M 元)
    public function get_preferential_price($type) {

        $count_num = $this->get_post_value($type);
        if (!($count_num > 0)) {
            return 0;
        }

        $result = $this->get_dvd_setting($type, 'preferential_price');
        return $result;
    }

    // 組合價 (多顏色一組)
    public function get_combination_price($type) {

        $count_num = $this->get_post_value($type);
        if (!($count_num > 0)) {
            return 0;
        }

        $combination_price = $this->get_dvd_setting($type, 'combination_price');
        $combination_count_num = $this->get_combination_count_num();

        $result = $combination_price * $combination_count_num;
        return $result;
    }

    // 原價
    public function get_original_price($type) {

        $count_num = $this->get_post_value($type);
        if (!($count_num > 0)) {
            return 0;
        }

        $original_price = $this->get_dvd_setting($type, 'original_price');
        $original_count_num = $this->get_original_count_num($type);

        $result = $original_price * $original_count_num;
        return $result;
    }

    // 總價格
    public function get_total_price($type) {

        $count_num = $this->get_post_value($type);
        if (!($count_num > 0)) {
            return 0;
        }

        $result = 0;
        $result += $this->get_preferential_price($type);
        $result += $this->get_combination_price($type);
        $result += $this->get_original_price($type);
        return $result;
    }

    // 總點數
    public function get_total_point($type) {

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

    public function get_dvd_info($type) {

        $result = array();
        $result['name'] = $this->get_dvd_setting($type, 'name');

        $count_num = $this->get_post_value($type);
        $result['count_num'] = $count_num;

        $result['more_free_count_num'] = $this->get_more_free_count_num($type, $count_num);
        return $result;
    }

    public function get_final_info() {

        // init
        $info = array(
            'error_num_types' => null,
            'dvds_info' => array(),
            'total_price' => 0,
            'total_point' => 0,
            'get_gift' => FALSE
        );

        $error_num_types = $this->get_error_num_types();
        if (!empty($error_num_types)) {
            $info['error_num_types'] = $error_num_types;
            return $info;
        }

        $dvds_type = $this->get_dvds_tpye();
        foreach ($dvds_type as $type) {
            $info['total_price'] += $this->get_total_price($type);
            $info['total_point'] += $this->get_total_point($type);
            $info['dvds_info'][$type] = $this->get_dvd_info($type);
        }

        if ($info['total_point'] >= _GIFT_NEEDED::$point) {
            $info['get_gift'] = TRUE;
        }

        return $info;
    }
}