<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QuestionModel extends Model
{
    protected $table = "question";
    protected $primaryKey = "question_id";
    protected $guarded = [];


    /**
     * @param $category integer
     * @param $keywords string
     * */
    public static function questions($category = 0, $keywords = ''){

        $perpage    = 1;
        $query      = new self();

        if($category > 0){
            $query = $query->where('category',$category);
        }

        if($keywords != ''){

            $query = $query->where(function($query) use ($keywords){
                $keywords = "%{$keywords}%";
               $query->where('title','like',$keywords)->orWhere('content','like',$keywords);
            });
        }

        $question  = $query->paginate($perpage);

        return $question;
    }


    /**
     * create a new question
     * @param $userId integer
     * @param $title string
     * @param $content string | not empty
     * @param $money float
     * @param $category integer
     * @param $draft integer | 1:draft 0:publish
     * @return object
     * */
    public static function create_question($userId,$title,$content,$money,$category,$draft){

        return self::create([
            'user_id'   => $userId,
            "title"     => $title,
            "content"   => $content,
            "money"     => $money,
            "category"  => $category,
            "draft"     => $draft,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
    }



    /**
     * @param $questionId integer
     * @param $newInfo array
     * @return boolean
     * */
    public static function update_question($questionId,$newInfo){

    }




}
