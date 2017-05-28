<?php
    require_once 'const.php';
    require_once 'dvd.php';
    require_once './lib/lib_calculate.php';

    class calculator {

        private $lib_calculate;

        private $info = array();

        function __construct($param = array()) {

            $this->lib_calculate = new lib_calculate();

            if (!empty($param)) {
                $this->lib_calculate->set_post_values_by_param($param);
            }
        }

        public function exec() {

            if (!$this->valid()) {
                $this->show_error_message();
                return;
            }

            $this->info = $this->lib_calculate->get_final_info();
            $this->show();
        }

        public function valid() {

            $error_num_types = $this->lib_calculate->get_error_num_types();
            $result = empty($error_num_types);
            return $result;
        }

        public function show_error_message() {

            $msg = array();
            $error_num_types = $this->lib_calculate->get_error_num_types();
            foreach($error_num_types as $type) {
                $type_name = $this->lib_calculate->get_dvd_setting($type, 'name');
                $msg[] = "{$type_name}數量請輸入非負整數!";
            }
            echo join('<br>', $msg);
        }

        public function show() {

            $msg = array();
            
            $info = $this->info;
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

            $total_price = $info['total_price'];
            $total_point = $info['total_point'];

            $msg['total_price'] = "總金額 {$total_price} 元";
            $msg['total_point'] = "此次消費積點為 {$total_point} 點";
            if ($total_point >= _GIFT_NEEDED::$point) {
                $msg['total_point'] .= "，恭喜您獲得神秘小禮物一份!";
            }
            echo join('<br>', $msg);
        }
    }