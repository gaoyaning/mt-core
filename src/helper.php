<?php
use App\Config\Config;

if (!function_exists('config')) {
    function config($key) {
        if (is_array($key)) {
            $arr = [];
            foreach ($key as $v) {
                $arr[$v] = include(config_path . "/$v.php");
            }
            return $arr;
        } else {
            return $config_path = include(config_path . "/$key.php");
        }
    }
}

if (!function_exists('env')) {
    function env() {
        $value = getenv($key);
        return $value;
    }
}
