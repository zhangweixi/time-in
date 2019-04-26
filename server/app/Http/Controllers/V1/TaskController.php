<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\TaskModel;

class TaskController extends Controller
{

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
    }

    public function get_task_type(Request $request){

        $userId     = $request->input('userId',0);
        $types      = DB::table('task_type')->where('user_id',$userId)->whereNull('deleted_at')->get();

        return \API::add('types',$types)->send();
    }


    public function create_task_type(Request $request){

        $userId     = $request->input('userId',0);
        $typeName   = trim($request->input('typeName'));
        if(empty($typeName)){
            return \API::send(2001,"类型名称不能为空");
        }
        //检查是否有了这个分类
        $type       = DB::table('task_type')
            ->where('type_name',$typeName)
            ->Where('user_id',$userId)
            ->first();


        if($type && is_null($type->deleted_at)){

            return \API::send(2002,"已存在");

        }elseif($type){

            $typeId = $type->type_id;
            DB::table('task_type')->where('type_id',$typeId)->update(['deleted_at'=>null]);

        }else{

            $typeId = DB::table('task_type')->insertGetId(['user_id'=>$userId,"type_name"=>$typeName,'created_at'=>now()]);
        }

        return \API::add('typeId',$typeId)->send(200,"添加成功");
    }


    public function delete_type(Request $request){

        $typeId     = $request->input('typeId');
        DB::table('task_type')->where('type_id',$typeId)->update(['deleted_at'=>now()]);

        return \API::send();
    }


    public function create_task(Request $request){

        $userId     = $request->input('userId');
        $taskType   = $request->input('typeId');

        //检查以前是是否结束
        $task       = DB::table('task')->where('type_id',$taskType)->where('user_id',$userId)->whereNull('finished_at')->first();

        if(is_null($task)){

            $taskType   = DB::table('task_type')->where('type_id',$taskType)->first();

            $taskId = DB::table('task')->insertGetId([
                'type_id'   => $taskType->type_id,
                'type_name' => $taskType->type_name,
                'status'    => 1,
                'user_id'   => $userId,
                'created_at'=> now(),
                'updated_at'=> now()
            ]);

            $task   = DB::table('task')->where('task_id',$taskId)->first();
        }

        $task->time = time() - strtotime($task->created_at);
        return \API::add('task',$task)->send();
    }


    public function change_task(Request $request){

        $taskId     = $request->input('taskId');
        $status     = $request->input('status');

        $task       = TaskModel::find($taskId);

        if($status == 0 && $task->status == 1){ //update before total time


            $task->time += time() - strtotime($task->updated_at);
            $task->status= 0;

        }elseif($status == 1 && $task->status == 0){

            $task->status = 1;
        }

        $task->update();

        return \API::send();
    }


    public function finish_task(Request $request){

        $taskId     = $request->input('taskId');
        $userId     = $request->input('userId');

        $task       = DB::table('task')->where('task_id',$taskId)->first();
        $time       = $task->time + (time() - strtotime($task->updated_at));

        DB::table('task')->where('task_id',$taskId)->update(['finished_at'=>now(),'time'=>$time]);

        return \API::send();
    }


    public function get_current_task(Request $request){

        $userId     = $request->input('userId');

        $taskInfo   = TaskModel::where('user_id',$userId)->whereNull('finished_at')->first();

        if($taskInfo->status == 1){

            $taskInfo->time = $taskInfo->time + (time() - strtotime($taskInfo->updated_at));

        }
        return \API::add('task',$taskInfo)->send();
    }

    public function get_task_list(Request $request){

        $userId     = $request->input('userId');
        $lastTaskId = $request->input('lastTaskId',0);
        $query      = DB::table('task')->where('user_id',$userId);

        if($lastTaskId > 0){

            $query->where('task_id',"<",$lastTaskId);
        }

        $taskList= $query->orderBy('task_id','desc')->limit(20)->get();

        return \API::add('tasks',$taskList)->send();
    }


    public function delete_task(Request $request){

        $taskId     = $request->input('taskId');

        $taskInfo   = TaskModel::find($taskId);

        if(is_null($taskInfo->finished_at)){

            return \API::send(2001,"本任务未结束");
        }

        TaskModel::where('task_id',$taskId)->delete();

        return \API::send();
    }

}
