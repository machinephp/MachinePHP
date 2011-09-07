<?php

namespace _m;

class debug
{
    private static $instance;

    public function load()
    {
        if (isset(self::$instance))
            return false; //@TODO: throw error

        $c = __CLASS__;
        self::$instance = new $c;

        return self::$instance;
    }

    private function __construct()
    {

    }

    public function __clone() { return false;}
}

?>
