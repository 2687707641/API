<?php

namespace app\api\controller;

Class User extends Common{

	/*		用户注册 		*/
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

	/*		用户登录			*/
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

	/*		用户修改密码			*/
	public function change_pwd(){
		/*		接收数据		*/
		$data = $this->params;
		/*		验证用户是否存在		*/
		$this->check_exist($data['user_phone'],1);
		/*		检测原密码是否正确		*/
		$db_ini_pwd = db('user')->where('user_phone',$data['user_phone'])->value('user_pwd');
		if ($db_ini_pwd !== $data['user_ini_pwd']) {
		    $this->return_msg(400, '原密码错误!');
		}
		/*		将新密码更新到数据库中	*/
		$res = db('user')->where('user_phone',$data['user_phone'])->setField('user_pwd',$data['user_new_pwd']);
		if($res!=false){
			$this->return_msg(400,'修改密码失败!');
		}else{
			$this->return_msg(200,'修改密码成功!');
		}
	}

	/*		用户找回密码		*/
	public function find_pwd(){
		/*		接收数据		*/
		$data = $this->params;
		/*		验证用户是否存在		*/
		$this->check_exist($data['user_phone'],1);
		/*		检查验证码是否正确	*/
		$this->check_code($data['user_name'],$data['code']);
		/*		修改数据库		*/
		$res = db('user')->where('user_phone',$data['user_phone'])->setField('user_pwd',$data['user_pwd']);
		if($res!=false){
			$this->return_msg(400,'修改密码失败!');
		}else{
			$this->return_msg(200,'修改密码成功!');
		}
	}


	public function upload_head_img() {
	    /*********** 接收参数  ***********/
	    $data = $this->params;
	    /*********** 上传文件,获得路径  ***********/
	    $head_img_path = $this->upload_file($data['user_icon'], 'head_img');
	    /*********** 存入数据库  ***********/
	    $res = db('user')->where('user_id', $data['user_id'])->setField('user_icon', $head_img_path);
	    if ($res) {
	        $this->return_msg(200, '头像上传成功!', $head_img_path);
	    } else {
	        $this->return_msg(400, '上传头像失败!');
	    }
	}

	/*		用户设定昵称		*/
	public function set_nickname(){
		/*		接收数据		*/
		$data = $this->params;
		/*		检测昵称是否重复		*/
		$res = db('user')->where('user_name', $data['nickname'])->find();
		if($res){
			$this->return_msg(400,'该昵称已被占用!');
		}
		/*		将新昵称更新到数据库		*/
		$res = db('user')->where('user_id',$data['user_id'])->setField('user_name',$data['nickname']);
		if(!$res){
			$this->return_msg(400,'修改昵称失败!');
		}else{
			$this->return_msg(200,'修改昵称成功!');
		}
	}


}
