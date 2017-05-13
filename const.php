<?php
    // type: 哪些種類的 dvd 同一組會有額外優惠
    // rate: 優惠價為幾折
    class _COMBINATION_SETTING {

        public static $type = array('red', 'green');
        const rate = 0.5;
    }

    // name:               dvd 標籤名字
    // base_price:         每一種 dvd 的基礎價格 (元)       買 N 片內都是 M 元
    // each_price:         每一種 dvd 的原始價格 (元/每片)
    // combination_price:  每一種 dvd 的組合價格 (元/每片)  某些種類的 dvd 同一組後會有額外優惠
    // each_point:         每一種 dvd 的集點 (點/每片)
    // max_point:          每一種 dvd 的集點上限 (點)
    // max_free_count_num: 每一種 dvd 可購買基礎價的上限 (片)
    class _DEFAULT_SETTING {

        public static $all = array(
            'red' => array(
                'name'               => '紅標',
                'base_price'         => 60,
                'each_price'         => 40,
                'combination_price'  => 0,
                'each_point'         => 3,
                'max_point'          => 15,
                'max_free_count_num' => 2
            ),
            'green' => array(
                'name'               => '綠標',
                'base_price'         => 30,
                'each_price'         => 12,
                'combination_price'  => 0,
                'each_point'         => 1,
                'max_point'          => 8,
                'max_free_count_num' => 3
            ),
            'blue' => array(
                'name'               => '藍標',
                'base_price'         => 25,
                'each_price'         => 10,
                'combination_price'  => 0,
                'each_point'         => 0,
                'max_point'          => 0,
                'max_free_count_num' => 3
            )
        );
    }