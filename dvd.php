<?php
class dvd {

    private static $all_type_setting;
    private $setting = array();

    function __construct($type) {

        $all_type_setting = _DEFAULT_SETTING::$all;
        $setting_param = $all_type_setting[$type];
        $this->set_setting($type, $setting_param);

        // update other setting
        $this->update_combination_price($type);
    }

    public function get_setting($key) {

        return $this->setting[$key];
    }

    public function set_setting($type, $param=array()) {

        $setting = array();
        foreach($param as $key => $val) {
            $setting[$key] = $val;
        }
        $this->setting = array_merge($this->setting, $setting);
    }

    public function update_combination_price($type) {

        $combination_type = _COMBINATION_SETTING::$type;
        if (!in_array($type, $combination_type)) {
            $this->set_setting($type, array('combination_price' => 0));
            return;
        }

        $original_price = $this->get_setting('original_price');
        $combination_price = (int)($original_price * _COMBINATION_SETTING::rate);
        $this->set_setting($type, array('combination_price' => $combination_price));
    }
}