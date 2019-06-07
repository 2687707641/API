<?php
namespace app\admin\validate;
use think\Validate;

class Book extends Validate {
    //验证规则
    protected $rule = [
        'book_name'  => 'require|unique:book|max:10|chsAlphaNum',
        'book_price' => 'require|number|gt:0',
        'book_num'   => 'require|number|gt:0',
    ];

    //验证提示
    protected $message = [
        'book_name.require'     => '书籍名称必须，不得为空',
        'book_name.unique'      => '书名不得重复',
        'book_name.max'         => '书籍名称过长',
        'book_name.chsAlphaNum' => '书籍名称只能是汉字、字母和数字',

        'book_price.require'    => '书籍价格必须，不得为空',
        'book_price.number'     => '书籍价格只能是数字（单位:元）',
        'book_price.gt'         => '书籍价格必须大于0',

        'book_num.require'      => '书籍数量必须，不得为空',
        'book_num.number'       => '书籍数量只能是数字',
        'book_num.gt'           => '书籍价格必须大于0',

        // 'cate_desc.require' => '栏目描述不得为空',
    ];

}