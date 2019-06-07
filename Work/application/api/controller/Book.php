<?php

namespace app\api\controller;

class Book extends Common {

    public function addBook() {
        /*        接收参数        */
        $data = $this->params;
        /*        检测分类是否存在        */
        if ($data['book_pid'] != 0) {
            $this->check_cate_id($data['book_pid']);
        } else {
            $this->return_msg(400, '所属分类错误!');
        }
        /*        检测书籍是否重名        */
        $this->check_repeat($data['book_name'], 'book');
        /*        加入数据库        */
        $res = db('book')->insert($data);
        if (!$res) {
            $this->return_msg(400, '添加书籍失败!');
        } else {
            $this->return_msg(200, '添加书籍成功!');
        }
    }

}
