<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserModel extends Model
{

    protected $table = "user";
    protected $primaryKey = "user_id";
    protected $guarded = [];
    /**
     * get user detail info by user id
     * @param $userId integer
     * @return mixed
     * */
    static function detail($userId){

        $detailInfo = self::find($userId);

        return $detailInfo;
    }


    static function get_mp_info($openId){

        return self::where('mp_openid',$openId)->first();
    }



    static function create_user($userInfo){

        $oldInfo    = empty($userInfo['unionid']) ? false : self::where('unionid',$userInfo['unionid'])->first();

        if($oldInfo){
            foreach($oldInfo as $key => $v){

                if(empty($v) || is_null($v)){

                    unset($oldInfo[$key]);
                }
            }

            self::where('user_id',$oldInfo->user_id)->update($userInfo);

            return self::detail($oldInfo->user_id);
        }

        return self::create($userInfo);
    }

    static function create_token($userId,$tokeType){

        $has    = DB::table('token')->where('user_id',$userId)->first();
        $token  = md5(create_member_number());

        if($has){

            DB::table('token')->where('user_id',$userId)->update([$tokeType=>$token,'created_at'=>now()]);

        }else{

            DB::table('token')->insert(['user_id'=>$userId,$tokeType=>$token,'created_at'=>now()]);

        }

        return $token;
    }
}