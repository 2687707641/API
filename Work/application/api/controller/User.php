<?php

namespace app\api\controller;

Class User extends Common{

	public function login(){
		/*		接收参数		*/
		$data = $this->params;
		/*		检测用户		*/
		$this->check_exist($data['user_name'],1);
		$db_res = db('user')
		            ->field('user_id,user_name,user_phone,user_rtime,user_pwd')
		            ->where('user_phone', $data['user_name'])
		            ->find();
		/*		检测密码		*/
		if ($db_res['user_pwd'] !== $data['user_pwd']) {
		        $this->return_msg(400, '用户名或者密码不正确!');
		    } else {
		        unset($db_res['user_pwd']); // 密码永不返回
		        $this->return_msg(200, '登录成功!', $db_res);
		    }
	}


	/*用户注册*/
	public function register(){
		/*		接收参数			*/
		$data = $this->params;
		/*		检查验证码		*/
		$this->check_code($data['user_name'],$data['code']);
		/*		检查用户名是否为手机号		*/
		$this->check_username($data['user_name']);
		/*		检查用户是否存在		*/
		$this->check_exist($data['user_name'],0);
		/*		将用户写入数据库		*/
		$data['user_phone'] = $data['user_name'];
		unset($data['user_name']); //避免错误写入用户昵称
		$data['user_rtime'] = time(); //记录注册时间		
		$res = db('user')->insert($data);
		// var_dump($data);die;
		if(!$res){
			$this->return_msg(400,'用户注册失败!');
		}else{
			$this->return_msg(200,'用户注册成功!');
		}
	}

}
