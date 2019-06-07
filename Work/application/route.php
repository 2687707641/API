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
// 添加栏目
Route::post('cate/add','cate/add_cate');

// 删除栏目
Route::get('cate/delete/:cate_id','cate/del_cate');

/*************书籍管理**********************/

Route::post('book/add','book/add_book');