<?php
require_once 'lib/lib_calculator.php';

class calculator {

    private $lib_calculator;
    private $info = array();

    function __construct() {

        $this->lib_calculator = new lib_calculator();
    }

    public function exec() {

        $this->info = $this->lib_calculator->get_final_info();
        $this->show();
    }

    public function show_error_message() {

        $info = $this->info;

        $msg = array();
        foreach($info['error_num_types'] as $type) {
            $type_name = $this->lib_calculator->get_dvd_setting($type, 'name');
            $msg[] = "{$type_name}數量請輸入非負整數!";
        }
        echo join('<br>', $msg);
    }

    public function show() {

        $info = $this->info;
        if (!empty($info['error_num_types'])) {
            $this->show_error_message();
            return;
        }

        $msg = array();
        
        $dvds_info = $info['dvds_info'];
        foreach ($dvds_info as $type => $dvd_info) {
            $type_name = $dvd_info['name'];
            $count_num = $dvd_info['count_num'];
            $more_free_count_num = $dvd_info['more_free_count_num'];

            $msg["{$type}_info"] = "您購買{$type_name} {$count_num} 片";
            if ($more_free_count_num > 0) {
                $msg["{$type}_info"] .= "，可以再多拿 " . strval($more_free_count_num)  . " 片，價格不變唷!";
            }
        }

        $total_price = (string)$info['total_price'];
        $total_point = (string)$info['total_point'];

        $msg['total_price'] = "總金額 {$total_price} 元";
        $msg['total_point'] = "此次消費積點為 {$total_point} 點";
        if ($info['get_gift']) {
            $msg['total_point'] .= "，恭喜您獲得神秘小禮物一份!";
        }
        echo join('<br>', $msg);
    }
}