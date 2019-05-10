<?php
namespace app\api\controller;
use think\Request; 
use think\Controller;
use think\Db;
use think\Validate;
use think\Image;

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

        'upload_head_img' => array(
          'user_id' => 'require|number|max:2',
          'user_icon' => 'require|image|filesize:2000000|fileExt:jpg,png,bmp,jepg',
        ),

        'set_nickname' => array(
          'user_id' => 'require|number|max:2',
          'nickname' => 'require|max:20|alphaDash',
        ),

        'change_pwd'  => array(
          'user_phone'    => 'require',  
          'user_ini_pwd' => 'require', //原密码     
          'user_new_pwd'     => 'require',
        ),

        'find_pwd'      => array(
          'user_phone'    => 'require',  
          'code' => 'require|number|length:6',
          'user_pwd'     => 'require',
        ),
        
			),

			'Code' => array(
				'get_code' => array(
					'username'  => 'require',
					'is_exist'  => 'require|number|length:1',
				),
			),

      'Cate' => array(
        'cate_add' => array(
            'cate_name' =>'require',
            'cate_pid' =>'require|number',
        ),

        'cate_list' => array(
        ),

        'cate_delete' => array(
            'cate_id' => 'require',
        ),
      ),

      'Book' => array(
        'book_add' => array(
            'book_name' => 'require|max:20',
            'book_price' => 'require|number',
            'book_pid' => 'require|number',
            'book_num' => 'require|number',
        ),

        'book_list' => array(
            'cate_id' => 'require|number',
        ),
      ),
	);

	protected function _initialize(){
        parent::_initialize();
        $this->request = Request::instance();
        // $this->check_time($this->request->only(['time']));//验证时间戳
       $this->params =  $this->check_params($this->request->param(true));//过滤参数

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
   		echo json_encode($return_data,JSON_UNESCAPED_UNICODE);die;  

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

   			/*		手机号应该在数据库中	*/
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


  public function upload_file($file,$type=''){
    // 将文件移动到根目录的public的uploads文件夹里，DS会用当前时间创建文件夹
    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'); 
    if($info){
      // dump($info->getSavename());die();
      $path = '/uploads/' . $info->getSavename();
      /*    裁剪图片    */
      if(!empty($type)){
        $this->image_edit($path,$type);
        return str_replace('\\', '/', $path);
      }      
    }else{
      $this->return_msg(400, $file->getError());
    }
  }

  public function image_edit($path, $type) {
      $image = Image::open(ROOT_PATH . 'public' . $path);
      switch ($type) {
      case 'head_img':
          $image->thumb(200, 200, Image::THUMB_CENTER)->save(ROOT_PATH . 'public' . $path);
          break;
      }
  }




  /*    检查栏目是否存在    */
  public function check_pid($pid){
      $pid_res = db('cate')->where('cate_id',$pid)->find();
      if(!$pid_res){
          $this->return_msg(400,'该上级栏目不存在!');
      }
  }

  /*    无限极栏目打印     */
  public function sortCate($data,$pid=0){
      static $arr=[];//定义一个空的数组
      foreach ($data as $k => $v) { //k是id  v是他的值
          if ($v['cate_pid']==$pid) {//如果是顶级栏目就将其放进数组里
              $arr[]=$v;
              $this->sortCate($data,$v['cate_id']);//找到顶级栏目后找其他栏目
          }
      }
     return $arr;
  }


  /*    删除栏目    */
  public function getChildIds($id){
    $data = db('cate')->select();//得到所有栏目的信息
    return $this->_getChildIds($data,$id);//运行下面私有方法 传参 所有栏目信息 和 传参栏目id
  }

  private function _getChildIds($data,$id){
    static $arr=array();//创建静态数组
    foreach ($data as $k => $v) {
        if ($v['cate_pid']==$id) {//所有栏目的pid 等于当前栏目id 则为子栏目
            $arr[]=$v['cate_id'];//获取其子栏目id 放到静态数组里面
            $this->_getChildIds($data,$v['cate_id']);//继续往下找  寻找子栏目的子栏目
        }
    }
    return $arr;
  }

  /*    检测书籍是否重名    */
  public function check_repeat($name){
    $res = db('book')
          ->where('book_name', $name)
          ->find();
    if($res){
      $this->return_msg(400,'该书籍已存在!');
    }
  }


  /**/
  public function select_list($id){

    $res = db('cate')
          ->where('cate_id', $id)
          ->find();

    /*    如果是顶级栏目就返回他的下级栏目,如果不是顶级栏目就返回该栏目下的所有书籍信息*/
    if($res['cate_pid']==0){
        $db_res = db('cate')
                  ->where('cate_pid', $id)
                  ->select();    
    }else{
      $db_res = db('book')
                ->where('book_pid', $id)
                ->select();
    }
     return $db_res;
  }






}