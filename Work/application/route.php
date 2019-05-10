<?php
use think\Route;
//域名重定向 路由绑定
Route::domain('api','api');

//获取验证码
Route::get('code/:time/:username/:is_exist','code/get_code');

/************用户管理**********************/
// 用户注册
Route::post('user/register','user/register');

// 用户登录
Route::post('user/login','user/login');

// 用户找回密码
Route::post('user/find_pwd','user/find_pwd');

// 用户上传头像
Route::post('user/icon','user/upload_head_img');

// 用户修改昵称
Route::post('user/nickname','user/set_nickname');

// 用户修改密码
Route::post('user/change_pwd','user/change_pwd');




/************栏目管理*******************/

// 添加栏目(添加分类)
Route::post('cate/add','cate/cate_add');

// 栏目列表(查询有哪些分类，以便于添加二级分类)
Route::get('cate/list','cate/cate_list');

// 删除栏目
Route::get('cate/del/:cate_id','cate/cate_delete');


/**************书籍管理**********************/

//添加商品(书籍)
Route::post('book/add','book/book_add');

//查看该栏目下的所有书籍
Route::get('book/book_list/:time/:cate_id','book/book_list'); 