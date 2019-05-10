<?php
namespace app\api\controller;

Class Cate extends Common{


	/*添加栏目*/
	public function cate_add(){
		/*接收参数*/
		$data = $this->params;
		/*检查上级栏目(分类)是否存在*/
		if($data['cate_pid']!=0){
			$this->check_pid($data['cate_pid']);
		}
		/*将此栏目(分类)加入数据库*/
		$data['cate_time'] = time();//得到时间
		$res = db('cate')->insert($data);
		if(!$res){
			$this->return_msg(400,'分类创建失败!');
		}else{
			$db_res = db('cate')
			            ->where('cate_name', $data['cate_name'])
			            ->find();
			$this->return_msg(200,'分类创建成功!',$db_res);
		}
	}


	/*		查询所有栏目		*/
	public function cate_list(){
		$data=db('cate')->order('cate_id')->select();
		$res = $this->sortCate($data);
		$this->return_msg(200,'查询栏目(分类)成功:',$res);
	}


	/*		删除栏目 		*/
	public function cate_delete(){
		/*		接收数据			*/
		$data = $this->params;
		/*		查询栏目是否存在		*/
		$this->check_pid($data['cate_id']);
		/*		获取栏目及其子栏目		*/
		$childIds =$this->getChildIds($data['cate_id']);//获取子栏目
		$childIds[] = intval($data['cate_id']); 
		/*		删除栏目		*/
		$res = db('cate')->delete($childIds);
		if (!$res) {
			$this->return_msg(400,'删除栏目失败!');
		}else{
			$this->return_msg(200,'删除栏目成功!');
		}
	}


}