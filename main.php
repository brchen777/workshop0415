<?php
require_once 'index.php';
require_once 'calculator.php';

function main() {
    echo "<br>";
    $calculator = new calculator();
    $calculator->exec();
}
main();