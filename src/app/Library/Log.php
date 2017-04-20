<?php
namespace App\Library;

class Log {
    const logfile = log_path . "/stub.log";

    public static function getLogHeader($type) {
        $date = date("Y-m-d H:i:s");
        $type = strtoupper($type);
        return $type . " " . '[' . $date . ']';
    }

    public static function info($head, $arr) {
        if (is_array($arr)) {
            $arr_json = json_encode($arr, JSON_UNESCAPED_UNICODE);
        } else {
            $arr_json = $arr;
        }
        $header = self::getLogHeader('info');
        $log = $header.' ['.$head.'] '.$arr_json."\n";
        file_put_contents(self::logfile, $log, FILE_APPEND);
    }
}
