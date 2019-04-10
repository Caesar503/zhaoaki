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
        // echo '公众号的id：'.$res->ToUserName.'<br>';
        // echo '用户的openid'.$res->FromUserName.'<br>';
        // echo '时间戳：'.$res->CreateTime.'<br>';
        // echo '消息类型：'.$res->MsgType.'<br>';
        // echo '事件类型：'.$res->Event.'<br>';
        // echo 'id：'.$res->EventKey.'<br>';die;

        //公众号的id
        $wzhid = $res->ToUserName;
        //用户的id
        $oid = $res->FromUserName;
        // echo $oid;
        //事件类型
        $event = $res->Event;

        if($event=='subscribe'){//扫码关注事件
            $local_user = App\Model\WxUser::where('openid',$oid)->first();
            // dd($local_user);
            if($local_user){//如果用户已经存在

            }else{//不存在
                // 通过openid 获取用户的信息
                $info = $this->get_userinfo($oid);
                // print_r($info);
                $u_info = [
                    'openid'    => $info['openid'],
                    'nickname'  => $info['nickname'],
                    'sex'  => $info['sex'],
                    'headimgurl'  => $info['headimgurl'],
                ];
                print_r($u_info);die;
            }
        }
        

        $time = date('Y-m-d H:i:s',time());
        $str = $time . $content . "\n";
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        echo 'success';
    }
    //获取access_token
    public function get_access(){
        $k = 'access_token';
        // Redis::delete($k);
        $token = Redis::get($k);
        if($token==''){
            // echo 'no chche:'."<br>";
            $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET');
            // echo env('WX_APPID');die;
            // echo $url;die;
            $key = 'wx_access_token';
            $response = file_get_contents($url);
            // dd($response);
            $res = json_decode($response,true);
            Redis::set($k,$res['access_token']);
            Redis::expire($k,3600);
            $token = $res['access_token'];
        }
        return $token;
    }
    //获取用户的信息
    public function get_userinfo($openid){
        $l = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->get_access()."&openid=".$openid."&lang=zh_CN";
        // dd($l);
        $data = file_get_contents($l);
        $u = json_decode($data,true);
        return $u;
    }
}
