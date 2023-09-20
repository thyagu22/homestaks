<?php

namespace App\Utils;

class utilidades
{
    public function __construct()
    {
    }

    public static function debug($vars)
    {
        echo "<pre>";
        print_r($vars);
        echo "<pre>";
        exit;

    }

}