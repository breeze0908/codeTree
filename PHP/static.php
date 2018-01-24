<?php

class a {

    static protected $test = "class a";

    public function static_test() {
        echo static::$test . PHP_EOL; // Results class b
        echo self::$test . PHP_EOL; // Results class a
    }
}

class b extends a {
    static protected $test = "class b";
}

$obj = new b();
$obj->static_test();
