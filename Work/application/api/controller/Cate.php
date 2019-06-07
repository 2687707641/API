<?php
namespace app\api\controller;

class Cate extends Common {

    /*        新增分类         */
    public function add_cate() {
        /*        接收参数        */
        $data = $this->params;
        /*        检查上级分类是否存在        */
        if ($data['cate_pid'] != 0) {
            $this->check_cate_id($data['cate_pid']);
        }
        /*        检查分类是否重名        */
        $this->check_repeat($data['cate_name'], 'cate');
        /*        写入数据库        */
        $res = db('cate')->insert($data);
        if ($res) {
            $this->return_msg(200, '新增分类成功！');
        } else {
            $this->return_msg(400, '新增栏目失败!');
        }
    }

    /*        删除栏目         */
    public function del_cate() {
        /*        接收参数        */
        $id = $this->params['cate_id'];
        /*        检测分类是否存在        */
        $this->check_cate_id($id);
        /*        获取栏目id及其子栏目id        */
        $childIds   = $this->getChildIds($id);
        $childIds[] = intval($id);
        /*        删除栏目及其子栏目        */
        $res = db('cate')->delete($childIds);
        if ($res) {
            $this->return_msg(200, '删除栏目成功!', $res);
        } else {
            $this->return_msg(400, '删除栏目失败!');
        }
    }
}
