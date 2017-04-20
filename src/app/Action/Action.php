<?php
namespace App\Action;

use App\Stub\Stub;
use App\Library\Http;

class Action {
    public $request_url = "request_url";
    public $post_url;
    public $stub;

    public function __construct() {
        $stub_conf = config('stub');
        $this->post_url = $stub_conf[$this->request_url];
        $this->stub = new Stub();
    }

    public function startStub($stub_data) {
        $this->stub->setStubData($stub_data);
        $this->stub->start();
    }

    public function stopStub() {
        $this->stub->stop();
        sleep(1);
    }

    public function getCheckData($request_data) {
        return Http::postJson($this->post_url, $request_data);
    }
}
