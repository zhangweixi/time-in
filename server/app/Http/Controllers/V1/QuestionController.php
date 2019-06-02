<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class QuestionController extends Controller
{

    public function add_quest_group(){

        $userId     = 10;
        $title      = "测试题库";
        $category   = "英语";

        $content    = file("C:\Users\Administrator\Desktop\\test.md");
        $questions  = [];
        $i          = -1;
        $lettles    = [",","，",".","。",";","；"];

        foreach($content as $str){

            //如果首字母是数字，则表示一个题目标题
            $str            = rtrim($str);
            $firstLettle    = substr($str,0,1);
            if(is_numeric($firstLettle)){

                $i++;

                $questions[$i] = [
                    "title"     =>$str,
                    'options'   =>[],
                    "type"      => "",
                    "answer"    => ""
                ];

                $ansth      = 0;    //答案的序号
                $end        = str_replace("|","|",mb_substr($str,mb_strlen($str)-3));
                $isJieda    = false;
                if( $end == "|解答"){

                    $isJieda    =  true;
                    $questions[$i]["type"]   = "JD";
                }

            }else{

                if($isJieda == true){ //如果是解答题，只需将答案汇集在一起就行

                    $questions[$i]["answer"] .= $str;
                    continue;
                }


                if(strlen(trim($str))==0){

                    continue;
                }

                //判断哪个是正确结果
                $lastLittle     = mb_substr($str,mb_strlen($str)-1);
                $isRight        = 0;

                if(in_array($lastLittle,$lettles)){

                    $isRight    = 1;
                    $str        = mb_substr($str,0,-1); //删除符号标记
                }

                $questions[$i]["options"][] = ["content"=>$str,"isRight"=>$isRight];
                $ansth++;
            }
        }

        //将题目存储在数据库
        $groupId = DB::table('quest_group')->insertGetId([
            "user_id"   => $userId,
            "title"     => $title,
            "category"  => $category,
            "quest_num" => count($questions),
            "created_at"=> now(),
            "updated_at"=> now()
        ]);

        foreach($questions as $quest){
            $ans    = $quest['type'] == "JD" ? $quest['answer'] : \GuzzleHttp\json_encode($quest['options']);
            DB::table('quest_list')->insert([
                "quest_group_id"    => $groupId,
                "title"             => $quest['title'],
                "type"              => $quest['type'],
                "answer"            => $ans
            ]);
        }

        return \API::send();
    }


    public function get_quest_group(){

        $userId     = 10;
        $questList  = DB::table('quest_group')->where('user_id',$userId)->get();
        dd($questList);
        return \API::add('questGroup',$questList)->send();
    }

    /*
     *
     * */
    public function get_quest(Request $request){

        $groupId    = $request->input("groupId");
        $questId    = $request->input('questId');

        if($questId > 0){ //如果指定了一个题，那就获取下一个，否则就获取第一个

            $questInfo  = DB::table('quest_list')
                ->where('quest_group_id',$groupId)
                ->where('quest_id',">",$questId)
                ->orderBy('quest_id')
                ->first();

        }else{

            $questInfo  = DB::table('quest_list')
                ->where('quest_group_id',$groupId)
                ->where('quest_id',">",$questId)
                ->orderBy('quest_id')
                ->first();
        }


        if($questInfo && $questInfo->type != "JD")
        {
            $questInfo->answer = \GuzzleHttp\json_decode($questInfo->answer);
        }
        return \API::add('questInfo',$questInfo)->send();
    }
}