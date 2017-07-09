<?php
// type: 哪些種類的 dvd 同一組會有組合優惠
// rate: 組合優惠為幾折
class _COMBINATION_SETTING {

    public static $type = array('red', 'green');
    const rate = 0.5;
}

// point: 獲得禮物最少需要的點數
class _GIFT_NEEDED {
    
    public static $point = 20;
}

// name:               dvd 標籤名字
// preferential_price: 每一種 dvd 買 N 片內總計是 M 的優惠價格 (元)
// original_price:     每一種 dvd 的原始價格 (元/每片)
// combination_price:  每一種 dvd 的組合價格 (元/每片)  某些種類的 dvd 同一組會有組合優惠
// each_point:         每一種 dvd 的集點 (點/每片)
// max_point:          每一種 dvd 的集點上限 (點)
// max_free_count_num: 每一種 dvd 可購買基礎價的上限 (片)
class _DEFAULT_SETTING {

    public static $all = array(
        'red' => array(
            'name'               => '紅標',
            'preferential_price' => 60,
            'original_price'     => 40,
            'combination_price'  => 0,
            'each_point'         => 3,
            'max_point'          => 15,
            'max_free_count_num' => 2
        ),
        'green' => array(
            'name'               => '綠標',
            'preferential_price' => 30,
            'original_price'     => 12,
            'combination_price'  => 0,
            'each_point'         => 1,
            'max_point'          => 8,
            'max_free_count_num' => 3
        ),
        'blue' => array(
            'name'               => '藍標',
            'preferential_price' => 25,
            'original_price'     => 10,
            'combination_price'  => 0,
            'each_point'         => 0,
            'max_point'          => 0,
            'max_free_count_num' => 3
        )
    );
}