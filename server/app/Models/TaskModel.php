<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaskModel extends Model
{
    protected $table = "task";
    protected $primaryKey = "task_id";
    protected $guarded = [];


    static function init_task($userId){

        $types  = ["学习","娱乐","运动"];
        $date   = now();

        foreach($types as $type){

            DB::table('task_type')->insert(['user_id'=>$userId,"type_name"=>$type,'created_at'=>$date]);

        }
    }
}
