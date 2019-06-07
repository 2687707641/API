<?php
namespace app\admin\validate;
use think\Validate;

class Cate extends Validate {
    //验证规则
    protected $rule = [
        'cate_name' => 'require|unique:cate|min:2|chsAlphaNum',
    ];

    //验证提示
    protected $message = [
        'cate_name.require'     => '栏目名称必须，不得为空',
        'cate_name.unique'      => '栏目不得重复',
        'cate_name.min'         => '栏目名称过短',
        'cate_name.chsAlphaNum' => '栏目名称只能是汉字、字母和数字',

        // 'cate_desc.require' => '栏目描述不得为空',

    ];

/*
//验证场景
protected $scene = [
'edit'  =>  ['cate_name'],
'add'  =>  ['cate_name','cate_desc'],
];
 */

}

/*// 执行验证
$validate = validate('cate');
if(!$validate->scene('add')->check($data)){
$this->error($validate->getError());

}*/