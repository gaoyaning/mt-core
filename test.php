<?php
define("base_path",dirname(__FILE__),true);
define("pid_path", base_path . "/pid", true);
define("config_path", base_path."/config", true);
require_once("vendor/autoload.php");
require_once("src/helper.php");
use App\Stub\Stub;
$obj = new Stub();
$arr = [
    "gyning" => "kxb",
];
$obj->setStubData($arr);
$obj->start();
sleep(10);
$obj->stop();
