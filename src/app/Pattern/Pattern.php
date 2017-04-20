<?php
namespace App\Pattern;

class Pattern {
    public $stub_data = [
        'zm' => [
            'zm' => [
                'status' => 1,
                'data'   => json_decode('{"content":{"score":"739"}}', true),
            ],
            'identify' => [
                'status' => 1,
                'data'   => json_decode('{"content":{"iVSVerifyResult":{"score":93,"resultMap":{"infocode_result_list":"[{\"code\":\"CERTNO_Match_Trust_Self_Sharing_Good\",\"description\":\"身份证号与其他信息匹配，匹配后的信息被一个用户使用\"},{\"code\":\"PHONE_Match_Trust_Self_Recency_Good\",\"description\":\"电话号码与其他信息匹配，匹配后的信息近期较活跃\"},{\"code\":\"NAME_Match_Sharing_Good\",\"description\":\"姓名与其他信息匹配，匹配后的信息被一个用户使用\"}]"},"isSuccess":true}},"biz_no":"ZM201702133000000737300647276617"}', true),
            ],
            'riskinfo' => [
                'status' => 1,
                'data'   => json_decode('{"content":{"isRisk":"F"},"zmproduct":"ZM10000029"}', true),
            ],
        ],
        'tongdun' => [
            'tongdun' => [
                'status' => 1,
                'data'   => json_decode('{"final_decision":"Accept","final_score":0}', true),
            ],
        ],
    ];

    public $request_body = [
        'partner_id' => '110001',
        'params' => '',
        'version' => '1.0',
        'ts' => 1479435821,
        'sign' => 'XWQGZ4z62MSrg+IbufB0jUeOHGyvLRjLwCSsoVCdA8xo8zWLT9rsv/+K678g7/negE76yAwGQUhPAjVflJ94asx1f91Z2qJ4XhwMqx9pWFcMMaV9djla55OAumLatflfyFNR05eachUctdaGUySiLYzTnyPqGFmr+sIa/sY59xQ=',
    ];

    public $request_json = '{"scene":"h_zm_score_credit","user_info":{"user_id":"4054146","name":"庞龙","user_name":"庞龙","mobile":"18860009195","id_number":"331023198708083154","customer_id":"268813897533674036534166269","user_status":{"alipay_user_id":"2088712514113995","gender":"m","user_status":"T","user_type_value":"2","is_id_auth":"T","is_mobile_auth":"T","is_bank_auth":"T","is_student_certified":"F","is_certify_grade_a":"T","is_certified":"T","is_licence_auth":"F","cert_type_value":"0","account_id":"11682645","order_num":0,"hasPaid":0},"ip_address":"117.136.75.84","is_test":0,"registed_at":"2016-02-2202:46:33","user_type":"normal","order_type":"qudian","iou_limit":0,"alipay_user_id":"2088712514113995","token_id":"6d3712cf580d59fe07d85e93d9419bc9","bqs_token_key":"3005ecdef7f6f4176bbb0caefff4eb24","latitude":"24.532611","longitude":"118.157503","user_address":[{"prov":"福建省","city":"厦门市","area":"湖里区","mobile":"18860009195","name":"庞龙","address":"火炬高技术开发区安>岭路989号>裕隆国际大厦707室"}]},"partner_id":"10003","microtime":"0.783923001478677845","account_id":10546073}';

    public function setStubData($stub_datas) {
        foreach ($stub_datas as $index => $value) {
            $this->arraySet($this->stub_data, $index, $value);
        }
        return $this->stub_data;
    }

    public function setRequestData($request_datas) {
        $partner_id = null;
        if (isset($request_datas['partner_id'])) {
            $partner_id = $request_datas['partner_id'];
            unset($request_datas['partner_id']);
        }

        $request_arr = json_decode($this->request_json, true);
        foreach ($request_datas as $indexs => $value) {
            $this->arraySet($request_arr, $indexs, $value);
        }
        $this->request_body['params'] = json_encode($request_arr);
        if ($partner_id) {
            $this->request_body['partner_id'] = $partner_id;
        }
        return $this->request_body;
    }

    public function arraySet(&$arr, $index, $value) {
        $index_key = explode(".", $index);
        $this->recursiveSet($arr, $index_key, $value);
    }

    public function recursiveSet(&$arr, $keys, $value) {
        $key = array_shift($keys);
        if (empty($keys)) {
            $arr[$key] = $value;
            return;
        } else {
            if (!isset($arr[$key])) {
                $arr[$key] = [];
            }
            $this->recursiveSet($arr[$key], $keys, $value);
        }
    }

    public function checkResult($diff_data, $check_data) {
        if (1 == $check_data['status']) {
            $check_data['data'] = json_decode($check_data['data'], true);
        }
        foreach ($diff_data as $index => $value) {
            $keys = explode('.', $index);
            $check_value = $this->recursiveGet($check_data, $keys);
            if ($value === $check_value) {
                continue;
            } else {
                return 1;
            }
        }
        return 0;
    }

    public function recursiveGet($arr, $keys) {
        $key = array_shift($keys);
        if (!isset($arr[$key])) {
            return null;
        } elseif (empty($keys)) {
            return $arr[$key];
        } else {
            $this->recursiveGet($arr[$key], $keys);
        }
    }
}
