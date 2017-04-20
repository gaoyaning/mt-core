<?php
namespace App\Config;

class Config {
    public static $config = null;
    private $data = array();

    public static function getInstance() {
        if (null != $config) {
            return $config;
        } else {
            $config = new self();
            return $config;
        }
    }

    public function get($key) {
        if (is_array($key)) {
            $arr = [];
            foreach ($key as $v) {
                $arr[$v] = require_once(config_path . "/$v.php");
            }
            return $arr;
        } else {
            return $config_path = require_once(config_path . "/$key.php");
        }
    }
}
