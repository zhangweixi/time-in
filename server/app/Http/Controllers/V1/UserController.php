<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use App\Models\TaskModel;
use App\Models\UserModel;
use Dingo\Api\Facade\API;
use Illuminate\Http\Request;
use App\Models\UserModel as MUser;


class UserController extends Controller
{

    public function login(Request $request){

        //微信端获取的code
        $code           = $request->input('code');

        //根据code来获取微信的openId
        $miniConfig     = config("wx.mini");

        $params         = [ "appid"     => $miniConfig["appId"],
            "secret"    => $miniConfig["appSecret"],
            "js_code"   => $code,
            "grant_type"=> "authorization_code",
        ];

        $params         = http_build_query($params);
        $url            = "https://api.weixin.qq.com/sns/jscode2session?".$params;
        $result         = file_get_contents($url);
        $wxInfo         = json_decode($result);


        if(!isset($wxInfo->openid)){

            return \API::send(2002,$wxInfo->errmsg);
        }


        $openId     = $wxInfo->openid;
        $sessionKey = $wxInfo->session_key;

        //检查用户是否存在
        $userInfo  = MUser::get_mp_info($openId);

        if(!$userInfo ){

            return \API::add('userInfo',['mp_openid'=>$openId])->send(2003,"用户未注册");

        }else{

            $userInfo->token = MUser::create_token($userInfo->user_id,'mp_token');

            return \API::add('userInfo',$userInfo)->send();
        }
    }


    public function register(Request $request){

        $client     = $request->input('client');

        $mpOpenid   = $request->input('mpOpenid');

        $nickname   = $request->input('nickName');

        $head       = $request->input('head','');

        $sex        = $request->input('sex','');

        $unionId    = $request->input('unionid','');

         //检查用户是否存在
        $userInfo  = MUser::get_mp_info($mpOpenid);
        
        if(!$userInfo){

            $newInfo = [
                'unionid'=>$unionId,
                'mp_openid'=>$mpOpenid,
                'head'=>$head,
                'sex'=>$sex,
                'nick_name'=>$nickname
            ];
            $userInfo   = MUser::create_user($newInfo);
            TaskModel::init_task($userInfo->user_id);   //初始化几个常用任务
        }

        $types      = ["wxmp"=>"mp_token"];

        $userInfo->token    = MUser::create_token($userInfo->user_id,$types[$client]);

        return \API::add('userInfo',$userInfo)->send();
    }

    /**
     * get single user detail info
     * @param $request Request
     * @return mixed
     * */
    public function detail(Request $request){

        $userId     = $request->input('userId');

        $detailInfo = MUser::detail($userId);

        return \API::add('userInfo',$detailInfo)->send();
    }
}