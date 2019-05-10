<?php
	
namespace app\api\controller;

Class Book extends Common{

	/*		添加书籍		*/
	public function book_add(){
		/*		接收数据		*/
		$data = $this->params;
		/*		检测所属分类是否存在		*/
		$this->check_pid($data['book_pid']);
		/*		判断是否是顶级栏目		*/
		$flag = db('cate')->where('cate_id',$data['book_pid'])->find();
		if($flag['cate_pid'] == 0){
			$this->return_msg(400,'顶级栏目不允许添加书籍!');
		}
		/*		检测该书籍是否存在		*/
		$this->check_repeat($data['book_name']);
		/*		将书籍加入数据库		*/
		$data['book_time'] = time();
		$res = db('book')->insert($data);
		if(!$res){
			$this->return_msg(400,'书籍添加失败');
		}else{
			$db_res = db('book')
			        ->where('book_name', $data['book_name'])
			        ->find();
			$this->return_msg(200,'书籍添加成功!',$db_res);
		}
	}


	/*		查看书籍列表		*/
	public function book_list(){
		/*		接收参数		*/
		$data = $this->params;
		/*检测该栏目(分类)是否存在*/
		$this->check_pid($data['cate_id']);
		/*查询数据库并返回数据(如果是顶级栏目就返回他的下级栏目,如果不是顶级栏目就返回该栏目下的所有书籍信息)*/
		$db_res = $this->select_list($data['cate_id']);	
		$this->return_msg(200,'查询成功',$db_res);
	}



}

