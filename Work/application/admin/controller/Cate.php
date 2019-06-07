<?php
namespace app\admin\controller;
use think\Controller;

class Cate extends controller {

    public function cate_lst() {
        $page     = 10;
        $top_cate = db('cate')->where('cate_pid', 0)->select();
        $data     = input('param.');
        // dump($data);
        // die();
        if (isset($data['val'])) {
            $val     = $data['val'] == '*' ? 0 : 1;
            $arr     = $data['arr'] == '' ? 2 : 4;
            $sel_val = $data['val'];
            $sel_arr = $data['arr'];
        } else {
            $val     = 0;
            $arr     = 2;
            $sel_val = '';
            $sel_arr = '';
        }
        $sel = $val + $arr;

        //2:所有类别无特定信息
        //3：特定类别无特定信息
        //4：所有类别特定信息
        //5：特定类别特定信息
        switch ($sel) {
        case '2':
            $sel     = db('cate')->paginate($page);
            $cateRes = $this->add_level($sel, 0);
            $num     = db('cate')->count();
            break;
        case '3':
            $sel     = db('cate')->where('cate_id|cate_pid', $data['val'])->order('cate_pid')->paginate($page);
            $cateRes = $this->add_level($sel, 1);
            $num     = db('cate')->where('cate_id|cate_pid', $data['val'])->count();
            break;
        case '4':
            $sel     = db('cate')->where('cate_name', 'like', "%{$data['arr']}%")->paginate($page);
            $cateRes = $this->add_level($sel, 1);
            $num     = db('cate')->where('cate_name', 'like', "%{$data['arr']}%")->count();
            break;
        case '5':
            $sel     = db('cate')->where('cate_pid', $data['val'])->where('cate_name', 'like', "%{$data['arr']}%")->paginate($page);
            $cateRes = $this->add_level($sel, 1);
            $num     = db('cate')->where('cate_pid', $data['val'])->where('cate_name', 'like', "%{$data['arr']}%")->count();
            break;
        }
        // 分配数据
        $this->assign([
            'cateRes'  => $cateRes,
            'top_cate' => $top_cate,
            'val'      => $sel_val,
            'arr'      => $sel_arr,
            'num'      => $num,
        ]);
        return view();
    }

    public function cate_add() {
        if (request()->ispost()) {
            $data = input('post.');
            // 执行验证
            $validate = validate('cate');
            if (!$validate->check($data)) {
                $this->error($validate->getError());

            }
            $res = model('cate')->save($data);
            if ($res) {
                $this->success('添加栏目成功!', url('cate_lst'));
            } else {
                $this->error('添加栏目失败!');
            }
        }
        $cateRes = model('cate')->cateTree();
        $this->assign([
            'cateRes' => $cateRes,
        ]);
        return view();
    }

    public function cate_del() {
        $id         = input('id');
        $childIds   = model('cate')->getChildIds($id);
        $childIds[] = intval($id);
        //dump($childIds);die();
        //删除栏目后删除栏目下文章S
        //$_childIds=implode(',',$childIds);//所有要删除的文章 用‘,’组合成一个字符串 1,2,3
        //db('article')->where('cate_id','in',$childIds)->delete();//批量删除
        //删除栏目后删除栏目下文章E
        $del = db('cate')->delete($childIds);
        if ($del !== false) {
            $this->success('删除栏目成功!', url('cate_lst'));
        } else {
            $this->error('删除栏目失败!');
        }
    }

    public function cate_edit() {
        if (request()->isPost()) {
            $data = input('post.');
            $save = model('cate')->update($data);
            // 执行验证
            $validate = validate('cate');
            if (!$validate->check($data)) {
                $this->error($validate->getError());

            }
            if ($save !== false) {
                $this->success('修改栏目成功', url('cate_lst'));
            } else {
                $this->error('修改栏目失败');
            }
        }
        //获取原始数据
        $id     = input('id');
        $myself = db('cate')->find($id);
        //获取其本身和其子栏目
        $childIds   = model('cate')->getChildIds($id);
        $childIds[] = intval($id);
        //分类
        $cateRes = model('cate')->cateTree();
        //分配数据
        $this->assign([
            'cateRes'  => $cateRes,
            'myself'   => $myself,
            'childIds' => $childIds,
        ]);
        return view();
    }

    private function add_level($cateRes, $flag) {
        if ($flag != 0) {
            $data = $cateRes->all();
            foreach ($data as $k => &$v) {
                if ($v['cate_pid'] == 0) {
                    $v['level'] = 0;
                } else {
                    $v['level'] = 1;
                }
                $cateRes[$k] = $v;
            }
        } else {
            $data = model('cate')->cateTree();
            foreach ($data as $k => &$v) {
                $cateRes[$k] = $v;
            }
        }
        return $cateRes;
    }
}
