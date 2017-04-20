<?php
namespace App\Stub;

class Stub {
    public $stub_data;
    public $stub_conf;
    public $accept_socket;
    public $pid;
    public $stub_ip = "stub_ip";
    public $stub_port = "stub_port";
    public $pid_file;

    public function __construct() {
        $this->stub_conf = config('stub');
        $this->pid_file = pid_path . "/" . basename(__FILE__, '.php');
        echo $this->pid_file . "\n";
    }

    public function socket($address, $port) {
        $max_backlog = 16;

        //Create, bind and listen to socket
        if(($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === FALSE) {
            echo "Create socket failed!\n";
            exit;
        }
        socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE,0);

        if((socket_bind($socket, $address, $port)) === FALSE) {
            echo "Bind socket failed!\n";
            exit;
        }

        if((socket_listen($socket, $max_backlog)) === FALSE) {
            echo "Listen to socket failed!\n";
            exit;
        }

        if (false === (socket_set_block($socket))) {
            echo "block to socket failed!\n";
            exit;
        }

        while(TRUE) {
            if (($accept_socket = socket_accept($socket)) === FALSE) {
                continue;
            } else {
                $this->accept_socket = $accept_socket;
                // 获取数据&解码
                $params = $this->readAndDecode();
                $response = $this->getStubData($params);
                $response = $this->setResponse($response);
                socket_write($accept_socket, $response, strlen($response));
                socket_close($accept_socket);
            }
        }
    }

    public function readAndDecode() {
        $buffer = socket_read($this->accept_socket, 4096);
        $data = $buffer;
        $return_data = [];
        $arr = explode("\r\n", $data);
        $size = count($arr) -1;
        foreach ($arr as $k => $v) {
            if (0 == $k) {
                list($method, $version) = explode(" ", $v);
                $return_data["method"] = $method;
            } elseif ($size == $k) {
                $return_data["data"] = $v;
            } elseif ($size -1 == $k) {
                continue;
            } else {
                list($key, $value) = explode(": ", $v);
                $return_data[$key] = $value;
            }
        }
        // 按报文长度再次获取报文
        $get_length = strlen($return_data['data']);
        $return_data['data'] = $return_data['data'] .socket_read($this->accept_socket, $return_data['Content-Length'] - $get_length);
        if (is_array($return_data['data'])) {
            return $return_data['data'];
        } else {
            return json_decode($return_data['data'], true);
        }
    }

    public function getStubData($request) {
        if (!is_array($request)) {
            $request = json_deocde($request, true);
        }
        if (isset($request['params'])) {
            $request = $request['params'];
            if (!is_array($request)) {
                $request = json_decode($request, true);
            }
        }
        if (isset($request['risk_type'])) {
            $risk_type = $request['risk_type'];
            $risk_sub_type = $request['risk_sub_type'];
            if (isset($this->stub_data[$risk_type]) && isset($this->stub_data[$risk_type][$risk_sub_type])) {
                return json_encode($this->stub_data[$risk_type][$risk_sub_type]);
            } else {
                return json_encode([
                        'status' => -1,
                        'msg' => '无法获取数据',
                        ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return json_encode($this->stub_data);
        }
    }

    public function setResponse($response) {
        $msg_length = strlen($response);
        $return_response = <<<EOF
HTTP/1.1 200 OK
Connection: close
Content-Type: application/json
Content-Length: $msg_length

$response
EOF;
        return $return_response;
    }

    public function start() {
        if (file_exists($this->pid_file)) {
            print_r(__CLASS__ . "already running\n");
            exit;
        }
        $pid = pcntl_fork();
        if (-1 == $pid) {
            die("could not fork");
        } elseif ($pid) {
        } else {
            posix_setsid();
            if (0 === pcntl_fork()) {
                file_put_contents($this->pid_file, posix_getpid());
                $this->socket($this->stub_conf[$this->stub_ip], $this->stub_conf[$this->stub_port]);
            } else {
                exit;
            }
        }
    }

    public function stop() {
        if (file_exists($this->pidfile)) {
            $pid = file_get_contents($this->pid_file);
            posix_kill($pid, 9);
            unlink($this->pid_file);
        }
    }

    public function setStubData($stub_data) {
        $this->stub_data = $stub_data;
    }
}
