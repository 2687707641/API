<?php
namespace app\api\controller;
use think\Request; 
use think\Controller;
use think\Db;
use think\Validate;

class Common extends Controller{
	protected $request; //用来处理参数
	protected $validater; //用来验证数据(参数)
	protected $params; //过滤后符合要求的参数

	protected $rules = array(
			'User' => array(
				'login' => array(
					'user_name' => 'require|max:20|number',
					'user_pwd'  => 'require|alphaDash',
				),

				'register' => array(
					'user_name' => 'require|max:20|alphaDash',
					'user_pwd'	=> 'require|alphaDash',
					'code' 		=> 'require|number|length:6',
				),
			),

			'Code' => array(
				'get_code' => array(
					'username'  => 'require',
					'is_exist'  => 'require|number|length:1',
				),
			),
	);

	protected function _initialize(){
        parent::_initialize();
        $this->request = Request::instance();
        // $this->check_time($this->request->only(['time']));//验证时间戳
       $this->params =  $this->check_params($this->request->except(['time']));//过滤参数

    }

    /**
	* 验证请求是否超时
	 * @param  [array] $arr [包含时间戳的参数数组]
	 * @return [json]      [检测结果]
	*/
    public function check_time($arr){
    	if(!isset($arr['time'])||intval($arr['time'])<=1){
    		$this->return_msg(400,'时间戳不正确!');
    	}

    	if(time()-intval($arr['time'])>60){
    		$this->return_msg(400,'请求超时!');
    	}
    }

    /**
	* api 数据返回
	 
	 * @param  [int] $code [结果码 200:正常/4**数据问题/5**服务器问题]
	 * @param  [string] $msg  [接口要返回的提示信息]
	 * @param  [array]  $data [接口要返回的数据]
	 * @return [string]       [最终的json数据]
	*/
   	public function return_msg($code,$msg='',$data=[]){
   		/*					组合数据					*/
   		$return_data['$code'] = $code;
   		$return_data['$msg'] = $msg;
   		$return_data['$data'] = $data;
   		/*            返回错误信息，并终止脚本       */
   		echo json_encode($return_data);die;  

   	}


   	/**
   	* 验证参数 参数过滤
   	 * @param  [array] $arr [除time和token外的所有参数]
   	 * @return [return]      [合格的参数数组]
   	*/
   	public function check_params($arr){
   		/*			获取验证规则          */
   		/*rules[控制器][方法]*/
   		$rule = $this->rules[$this->request->controller()][$this->request->action()];
   		/*		验证参数并返回错误		*/
   		$this->validater = new Validate($rule);
   		if(!$this->validater->check($arr)){
   			$this->return_msg(400,$this->validater->getError());
   		}
   		/*		如果正确，通过验证		*/
   		return $arr;
   	}

   	/*		检测username是否为正确的手机号		*/
   	public function check_username($username){
   		$is_phone = preg_match('/^1[34578]\d{9}$/', $username);

   		if(!$is_phone){
   			$this->return_msg(400,'手机号不正确!');
   		}
   	}


   	/*		检测用户是否在数据库中存在			*/
   	public function check_exist($username,$exist){
   		$phone_res = db('user')->where('user_phone', $username)->find(); //查到返回1 没有返回NULL
   		switch ($exist) {
   			/* 		手机号不应该在数据库中		*/
   			case '0':
   				if($phone_res){
   					$this->return_msg(400,'此手机号已被占用!');
   				}
   				break;

   			/*		手机号不应该在数据库中	*/
   			case '1':
   				if(!$phone_res){
   					$this->return_msg(400,'此手机号不存在!');
   				}
   				break;
   		}
	}

	public function check_code($user_name,$code){
		/*		检查时间是否超时		*/
		// $last_time = session($user_name,'_last_send_time');
		// if(time() - $last_time > 600){
		// 	$this->return_msg(400,'验证超时，请在五分钟内验证!');
		// }

		/*		检查验证码是否正确		*/
		if(session($user_name."_code")!= $code){
			$this->return_msg(400,'验证码错误!');
		}

		/*		验证码只验证一次		*/
		// session($user_name.'_code',null);
	}

}