<?php
    require_once __DIR__ . '/../lib/lib_calculator.php';
    
    class calculatorTest extends PHPUnit_Framework_TestCase {

        private static $lib;
        public static function setUpBeforeClass() {
            self::$lib = new lib_calculator();
        }

        // 全部不輸入
        public function test_all_empty() {

            $param = array(
                'red'   => null,
                'green' => null,
                'blue'  => null
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $error_num_types = $final_info['error_num_types'];

            $this->assertEquals($error_num_types, array('red', 'green', 'blue'));
        }

        public function test_red1_green0_blue0() {

            $param = array(
                'red'   => 1,
                'green' => 0,
                'blue'  => 0
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];
            
            $this->assertEquals($dvds_info['red']['more_free_count_num'],   1);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 60);
            $this->assertEquals($final_info['total_point'], 3);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        public function test_red0_green1_blue0() {

            $param = array(
                'red'   => 0,
                'green' => 1,
                'blue'  => 0
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 2);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 30);
            $this->assertEquals($final_info['total_point'], 1);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        public function test_red0_green0_blue1() {

            $param = array(
                'red'   => 0,
                'green' => 0,
                'blue'  => 1
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  2);
            $this->assertEquals($final_info['total_price'], 25);
            $this->assertEquals($final_info['total_point'], 0);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        public function test_red1_green1_blue1() {

            $param = array(
                'red'   => 1,
                'green' => 1,
                'blue'  => 1
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   1);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 2);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  2);
            $this->assertEquals($final_info['total_price'], 115);
            $this->assertEquals($final_info['total_point'], 4);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        // 邊界
        public function test_red0_green0_blue0() {

            $param = array(
                'red'   => 0,
                'green' => 0,
                'blue'  => 0
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 0);
            $this->assertEquals($final_info['total_point'], 0);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        // 邊界
        public function test_all_negative() {

            $param = array(
                'red'   => -1,
                'green' => -1,
                'blue'  => -1
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $error_num_types = $final_info['error_num_types'];

            $this->assertEquals($error_num_types, array('red', 'green', 'blue'));
        }

        // 上邊界
        public function test_red1000_green1000_blue1000() {

            $param = array(
                'red'   => 1000,
                'green' => 1000,
                'blue'  => 1000
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 36047);
            $this->assertEquals($final_info['total_point'], 23);
            $this->assertEquals($final_info['get_gift'],    TRUE);
        }

        // 分段
        public function test_red2_green3_blue3() {

            $param = array(
                'red'   => 2,
                'green' => 3,
                'blue'  => 3
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 115);
            $this->assertEquals($final_info['total_point'], 9);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        // 未達標的分段點
        public function test_red1_green2_blue2() {

            $param = array(
                'red'   => 1,
                'green' => 2,
                'blue'  => 2
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   1);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 1);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  1);
            $this->assertEquals($final_info['total_price'], 115);
            $this->assertEquals($final_info['total_point'], 5);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        // 異常值
        public function test_redA_greenB_blueC() {

            $param = array(
                'red'   => 'a',
                'green' => 'b',
                'blue'  => 'c'
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $error_num_types = $final_info['error_num_types'];

            $this->assertEquals($error_num_types, array('red', 'green', 'blue'));
        }

        // 數字先寫的異常值
        public function test_red0A_green9B_blue8C() {

            $param = array(
                'red'   => '0a',
                'green' => '9b',
                'blue'  => '8c'
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $error_num_types = $final_info['error_num_types'];

            $this->assertEquals($error_num_types, array('red', 'green', 'blue'));
        }

        // 異常值
        public function test_all_9999999999999999() {

            $param = array(
                'red'   => '9999999999999999',
                'green' => '9999999999999999',
                'blue'  => '9999999999999999'
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];
            
            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], '77309411339');
            $this->assertEquals($final_info['total_point'], 23);
            $this->assertEquals($final_info['get_gift'],    TRUE);
        }

        // 紅標超出上邊界
        public function test_red3_green3_blue3() {

            $param = array(
                'red'   => 3,
                'green' => 3,
                'blue'  => 3
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 155);
            $this->assertEquals($final_info['total_point'], 12);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        // 綠標超出上邊界
        public function test_red2_green4_blue3() {

            $param = array(
                'red'   => 2,
                'green' => 4,
                'blue'  => 3
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 127);
            $this->assertEquals($final_info['total_point'], 10);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        // 藍標超出上邊界
        public function test_red2_green3_blue4() {

            $param = array(
                'red'   => 2,
                'green' => 3,
                'blue'  => 4
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 125);
            $this->assertEquals($final_info['total_point'], 9);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        // 紅綠標 1 組合
        public function test_red3_green4_blue3() {

            $param = array(
                'red'   => 3,
                'green' => 4,
                'blue'  => 3
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 141);
            $this->assertEquals($final_info['total_point'], 13);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        // 紅藍標 1 組合
        public function test_red3_green3_blue4() {

            $param = array(
                'red'   => 3,
                'green' => 3,
                'blue'  => 4
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 165);
            $this->assertEquals($final_info['total_point'], 12);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        // 綠藍標 1 組合
        public function test_red2_green4_blue4() {

            $param = array(
                'red'   => 2,
                'green' => 4,
                'blue'  => 4
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 137);
            $this->assertEquals($final_info['total_point'], 10);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }

        // 紅綠藍標 1 組合
        public function test_red3_green4_blue4() {

            $param = array(
                'red'   => 3,
                'green' => 4,
                'blue'  => 4
            );
            self::$lib->set_post_values_by_param($param);
            $final_info = self::$lib->get_final_info();
            $dvds_info = $final_info['dvds_info'];

            $this->assertEquals($dvds_info['red']['more_free_count_num'],   0);
            $this->assertEquals($dvds_info['green']['more_free_count_num'], 0);
            $this->assertEquals($dvds_info['blue']['more_free_count_num'],  0);
            $this->assertEquals($final_info['total_price'], 151);
            $this->assertEquals($final_info['total_point'], 13);
            $this->assertEquals($final_info['get_gift'],    FALSE);
        }
    }