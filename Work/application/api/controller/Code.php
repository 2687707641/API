<?php

namespace app\api\controller;

use submail\messagexsend;

class Code extends Common {
    public function get_code() {
        $username = $this->params['username'];
        $exist    = $this->params['is_exist'];
        /*检测username是不是手机号*/
        $this->check_username($username);
        /*        确认完毕，获取验证码*/
        $this->get_code_by_phone($username, $exist);
    }

    public function get_code_by_phone($username, $exist) {
        /*        检测手机号是否存在数据库中        */
        $this->check_exist($username, $exist);
        /*        检测验证码发送频率，30秒一次        */

        // if(session("?" . $username . '_last_send_time')){
        //     if(time()-session("?" . $username . '_last_send_time')<30){
        //         $this->return_msg(400,'验证码30s发送一次');
        //     }
        // }

        /*        生成验证码        */
        $code = $this->make_code(6);

        /*        使用seesion储存验证码，方便比对(验证码前缀用手机号区分)        */
        session($username . '_code', $code);
        /*        使用session储存验证码发送时间            */
        session($username . '_last_send_time', time());

        /*        发送验证码        */
        $this->send_code_to_phone($username, $code);
    }

    /*        生成验证码        */
    public function make_code($num) {
        $max = pow(10, $num) - 1; //999999
        $min = pow(10, $num - 1); //100000
        return rand($min, $max);
    }

    /*        发送验证码        */
    public function send_code_to_phone($phone, $code) {
        /*使用赛迪云SDK发送手机验证码*/
        $submail = new MESSAGEXsend();
        $submail->SetTo($phone);
        $submail->SetProject('HqdGa4');
        $submail->AddVar('code', $code);
        $submail->AddVar('time', 600);
        $xsend = $submail->xsend();
        if ($xsend['status'] !== 'success') {
            $this->return_msg(400, $xsend['msg']);
        } else {
            $this->return_msg(200, '手机验证码已发送, 每天最多发送5次, 请在一分钟内验证!');
        }
    }
}
