<?php
use think\Route;
//域名重定向 路由绑定
Route::domain('api','api');

//获取验证码
Route::get('code/:time/:username/:is_exist','code/get_code');

//
Route::post('user','user/login');

// 用户注册
Route::post('user/register','user/register');

// 用户登录
Route::post('user/login','user/login');