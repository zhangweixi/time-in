<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{


    public function get_task_type(Request $request){

        $userId     = $request->input('userId',0);

        $types      = DB::table('task_type')->where('user_id',0)->orWhere('user_id',$userId)->get();

        return \API::add('types',$types)->send();

    }


    public function create_task_type(Request $request){

        $userId     = $request->input('userId',0);
        $typeName   = trim($request->input('typeName'));

        //检查是否有了这个分类
        $type       = DB::table('task_type')
            ->where('type_name',$typeName)
            ->where(function($query) use ($userId) {

                $query->where('user_id',0)->orWhere('user_id',$userId);

            })->first();
        if($type){


            $typeId = $type->type_id;
            DB::table('task_type')->where('type_id',$typeId)->update(['deleted_at'=>null]);

        }else{

            $typeId = DB::table('task_type')->insertGetId(['user_id'=>$userId,"type_name"=>$typeName,'created_at'=>now()]);
        }

        return \API::add('typeId',$typeId)->send();
    }



    public function create_task(Request $request){

        $userId     = $request->input('userId');
        $taskType   = $request->input('typeId');

        //检查以前是是否结束
        $task       = DB::table('task')->where('type_id',$taskType)->where('user_id',$userId)->first();

        if(is_null($task)){

            $taskType   = DB::table('task_type')->where('type_id',$taskType)->first();

            $taskId = DB::table('task')->insertGetId([
                'type_id'   => $taskType->type_id,
                'type_name' => $taskType->type_name,
                'user_id'   => $userId,
                'created_at'=> now(),
                'updated_at'=> now()
            ]);

            $task   = DB::table('task')->where('task_id',$taskId)->first();
        }

        $task->time = time() - strtotime($task->created_at);
        return \API::add('task',$task)->send();
    }



    public function finish_task(Request $request){

        $taskId     = $request->input('taskId');
        $userId     = $request->input('userId');

        $task       = DB::table('task')->where('task_id',$taskId)->first();
        $time       = $task->time + (time() - strtotime($task->updated_at));

        DB::table('task')->where('task_id',$taskId)->update(['finished_at'=>now(),'time'=>$time]);

        return \API::send();
    }



    public function get_task_list(Request $request){

        $userId     = $request->input('userId');

        $taskList   = DB::table('task')->where('user_id',$userId)->paginate(20);

        return \API::add('tasks',$taskList)->send();
    }
}
