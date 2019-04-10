<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App;
class TextController extends Controller
{
    public function test(){
    	
    	// var_dump($res1);
    	$k = '全部信息';
    	$res1 = Redis::get($k);
    	$res1 = json_decode($res1);
    	if($res1==''){
    		echo '没有缓存';
    		$res1 = App\Test::all()->toArray();
    		$res1 = json_encode($res1);
    		$res = Redis::set($k,$res1,1);
    		var_dump($res);
    	}
    	var_dump($res1);
    	return view('Test.test');
    }
    //微信第一次接受
    public function wenxin_vaild(){
        echo $_GET['echostr'];
    }
    //微信第二次接受
    public function wenxin_vailde(){
        $contents = file_get_contents("php://input");
        $time = date('Y-m-d H:i:s',time());
        $str = $time . $contents . "\n";
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        echo 'success';
    }
}
