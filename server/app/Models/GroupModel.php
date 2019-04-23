<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class GroupModel extends Model
{
    protected $table = "answer_group";

    protected $primaryKey = "answer_group_id";

    protected $guarded = [];

    /**
     * get all group of question
     * @param $questionId integer
     * @return object
     * */
    public static function groups($questionId){

        $groups = DB::table('answer_group as a')
            ->leftJoin('user as b','a.answer_user_id','=','b.user_id')
            ->select('b.nick_name','b.head','a.created_at','a.creater_unread_num','a.answer_unread_num')
            ->where('question_id',$questionId)
            ->whereNull('deleted_at')
            ->where('a.active',1)
            ->get();

        return $groups;
    }


    /**
     * create a new group
     * @param $questionId integer
     * @param $answerUserId integer
     * @param $createrUserId integer
     * */
    public static function create_group($questionId,$answerUserId,$createrUserId){

        //check whether already exists
        $colum  = [
            'question_id'       =>$questionId,
            'answer_user_id'    =>$answerUserId,
            'creater_user_id'   =>$createrUserId
        ];

        $group = self::where($colum)->first();

        // if not exists , create a new group

        if(is_null($group)){

            $group = self::create($colum);
        }

        return $group->answer_group_id;
    }

}
