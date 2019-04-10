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
//测试
Route::any('/Text/test','Test\TextController@test');
<<<<<<< HEAD

//微信接口
Route::get('/Wenxin/vaild','Test\TextController@wenxin_vaild');
=======
Route::get('/info', function () {
    phpinfo();
});
>>>>>>> 0b00462675cfe34470ed3cc8725e7ae22f966c6b
