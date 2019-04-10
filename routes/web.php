<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/info', function () {
    phpinfo();
});
//测试
Route::any('/Text/test','Test\TextController@test');


//微信第一次接口
Route::get('/Wenxin/vaild','Test\TextController@wenxin_vaild');
//微信第二次接口
Route::post('/Wenxin/vaild','Test\TextController@wenxin_vailde');
//获取access_token
Route::post('Wenxin/access','Test\TextController@get_access');
