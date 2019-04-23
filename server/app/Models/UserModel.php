<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserModel extends Model
{

    protected $table = "user";
    protected $primaryKey = "user_id";

    /**
     * get user detail info by user id
     * @param $userId integer
     * @return mixed
     * */
    static function detail($userId){

        $detailInfo = self::find($userId);

        return $detailInfo;
    }


}