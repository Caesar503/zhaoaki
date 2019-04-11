<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App;
use GuzzleHttp\Client;
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
        $time = date('Y-m-d H:i:s',time());
        $str = $time . $content . "\n";
        //写入日志
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);

        //公众号的id
        $gzhid = $res->ToUserName;
        //用户的id
        $oid = $res->FromUserName;
        // echo $oid;
        //事件类型
        $event = $res->Event;
        //消息类型
        $MsgType = $res->MsgType;
        // echo $MsgType;die;



        if($event=='subscribe'){//扫码关注事件
            $local_user = App\Model\WxUser::where('openid',$oid)->first();
            // dd($local_user);
            if($local_user){//如果用户已经存在
                echo "<xml><ToUserName><![CDATA[$oid]]></ToUserName><FromUserName><![CDATA[$gzhid]]></FromUserName><CreateTime>".time()."</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[欢迎回来{$local_user->nickname}]]></Content></xml>";
            }else{//不存在
                // 通过openid 获取用户的信息
                $info = $this->get_userinfo($oid);
                // print_r($info);die;
                $u_info = [
                    'openid'    => $oid,
                    'nickname'  => $info['nickname'],
                    'sex'  => $info['sex'],
                    'headimgurl'  => $info['headimgurl'],
                ];
                $id =  App\Model\WxUser::insertGetId($u_info);
                echo "<xml><ToUserName><![CDATA[$oid]]></ToUserName><FromUserName><![CDATA[$gzhid]]></FromUserName><CreateTime>".time()."</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[欢迎关注{$info['nickname']}]]></Content></xml>";
            }
        }else if($MsgType=='text'){
                echo 1111;
        }
        

        
        // echo 'success';
    }
    //获取access_token
    public function get_access(){
        $k = 'access_token';
        $token = Redis::get($k);
        if($token==''){
            // echo 'no chche:'."<br>";
            $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET');
            // echo env('WX_APPID');die;
            // echo $url;die;
            $response = file_get_contents($url);
             // dd($response);
            $res = json_decode($response,true);
            // print_r($res);die;
            $token = $res['access_token'];
            Redis::set($k,$token);
            Redis::expire($k,3600);
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
    //创建公众号菜单
    public function create_menu(){
        //拼接借口
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->get_access();
        // echo $url;die;
        // 接口数据
        $data = [
            'button'=>[
               [
                 'type'=>'click',
                    'name'=>'赵恺',
                    'key'=>'key_menu_001'
               ],

               [
                 'type'=>'click',
                    'name'=>'wangjiao',
                    'key'=>'key_menu_002'
               ],
            ]
        ];
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        // $menu = urldecode($json_data);
                        // dd($menu);
        // 发送请求
        $client = new Client();
        $res1 = $client->request('POST',$url,[
            'body'=>$json_data
        ]);
                        // dd($res1);
        // 处理请求
        $res = $res1->getBody();
        $res = json_decode($res);
        if($res->errcode == 0){
            echo "创建菜单成功";
        }else{
            echo "创建菜单失败";
        }
    }
}
