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
        $content = file_get_contents("php://input");
        //解析xml数据
        $res  = simplexml_load_string($content);
        //公众号的id
        echo '公众号的id：'.$res->ToUserName.'<br>';
        echo '用户的openid'.$res->FormUserName.'<br>';
        echo '时间戳：'.$res->CreateTime.'<br>';
        echo '消息类型：'.$res->MsgType.'<br>';
        echo '事件类型：'.$res->Event.'<br>';
        echo 'id：'.$res->EventKey.'<br>';die;

        $time = date('Y-m-d H:i:s',time());
        $str = $time . $content . "\n";
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        echo 'success';
    }
    //获取access_token
    public function get_access(){
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'APPID&secret='.env('WX_APPSECRET');
        // echo $url;
        $key = 'wx_access_token';
        $response = file_get_contents($url);
        $res = json_decode($response,true);
        dd($res);
    }
}
