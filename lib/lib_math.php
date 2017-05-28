<?php
    class lib_math {

        public function is_native_number($num_str) {

            $result = (preg_match('/^\d+$/', $num_str)) ? TRUE : FALSE;
            return $result;
        }

        public function is_empty_number($num_str) {

            $result = ($num_str === '') ? TRUE : FALSE;
            return $result;
        }
    }