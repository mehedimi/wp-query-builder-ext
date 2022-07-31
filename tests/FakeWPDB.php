<?php

namespace Mehedi\WPQueryBuilderTestsExt;

class FakeWPDB
{
    static $methods = [];

    public $prefix = 'wp_';

    static function add($name, $callback) {
        self::$methods[$name] = $callback;
    }

    function __call($name, $args) {
        return self::$methods[$name](...$args);
    }
}