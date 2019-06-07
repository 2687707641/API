<?php
namespace app\admin\controller;
use think\Controller;

Class Book extends controller {

    public function book_add() {
        if (request()->ispost()) {
            $data = input('post.');
            // 执行验证
            $validate = validate('book');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            if (fmod($data['book_num'], 1) != 0) {
                $this->error('书籍数量只能是正整数！');
            }
            $data['book_time'] = time();
            $res               = db('book')->insert($data);
            if ($res) {
                $this->success('上架书籍成功!', url('book_lst'));
            } else {
                $this->error('上架书籍失败失败!');
            }
        }
        $cateRes    = model('cate')->cateTree();
        $catePid    = db('cate')->field(array('cate_id'))->where('cate_pid', 0)->select();
        static $arr = array(); //创建静态数组
        foreach ($catePid as $k => $v) {
            $arr[] = $v['cate_id'];
        }
        $catePid = $arr;
        $this->assign([
            'cateRes' => $cateRes,
            'catePid' => $catePid,
        ]);
        return view();
    }

    public function book_lst() {
        $page = 10;
        $data = input('param.');
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
            $bookRes = db('book')->order('cate_pid')->paginate($page);
            $num     = db('book')->count();
            break;
        case '3':
            $bookRes = db('book')->where('cate_pid', $data['val'])->paginate($page);
            $num     = db('book')->where('cate_pid', $data['val'])->count();
            break;
        case '4':
            $bookRes = db('book')->where('book_name', 'like', "%{$data['arr']}%")->order('cate_pid')->paginate($page);
            $num     = db('book')->where('book_name', 'like', "%{$data['arr']}%")->count();
            break;
        case '5':
            $bookRes = db('book')->where('cate_pid', $data['val'])->where('book_name', 'like', "%{$data['arr']}%")->paginate($page);
            $num     = db('book')->where('cate_pid', $data['val'])->where('book_name', 'like', "%{$data['arr']}%")->count();
            break;
        }
        $cateRes    = model('cate')->cateTree();
        $catePid    = db('cate')->field(array('cate_pid'))->select();
        $bookRes    = $this->add_cate_name($bookRes);
        static $arr = array(); //创建静态数组
        foreach ($catePid as $k => $v) {
            $arr[] = $v['cate_pid'];
        }
        $catePid = array_unique($arr); //去重复的值
        $this->assign([
            'val'     => $sel_val,
            'arr'     => $sel_arr,
            'bookRes' => $bookRes,
            'catePid' => $catePid,
            'cateRes' => $cateRes,
            'num'     => $num,
        ]);
        return view();
    }

    public function book_edit() {
        if (request()->isPost()) {
            $data = input('post.');
            $save = model('book')->update($data);
            // 执行验证
            $validate = validate('book');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            if (fmod($data['book_num'], 1) != 0) {
                $this->error('书籍数量只能是正整数！');
            }

            if ($save !== false) {
                $this->success('修改商品信息成功', url('book_lst'));
            } else {
                $this->error('修改修改商品信息失败');
            }
        }
        $id         = input('id');
        $myself     = db('book')->find($id); //获取自身信息
        $cateRes    = model('cate')->cateTree();
        $catePid    = db('cate')->field(array('cate_pid'))->select();
        static $arr = array(); //创建静态数组
        foreach ($catePid as $k => $v) {
            $arr[] = $v['cate_pid'];
        }
        $catePid = array_unique($arr); //去重复的值
        $this->assign([
            'myself'  => $myself,
            'cateRes' => $cateRes,
            'catePid' => $catePid,
        ]);
        return view();
    }

    public function book_del() {
        $id  = input('id');
        $del = db('book')->delete($id);
        if ($del !== false) {
            $this->success('删除书籍成功!', url('book_lst'));
        } else {
            $this->error('删除书籍失败!');
        }
    }

    private function add_cate_name($bookRes) {
        $data      = $bookRes->all();
        $cate_name = db('cate')->field('cate_id,cate_name')->select();
        foreach ($data as $k => $v) {
            foreach ($cate_name as $key => $value) {
                if ($value['cate_id'] == $v['cate_pid']) {
                    $v['cate_name'] = $value['cate_name'];
                }
            }
            $bookRes[$k] = $v;
        }
        return $bookRes;
    }

}