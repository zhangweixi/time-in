<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class QuestionController extends Controller
{
    /**
     * 生成CODE
     * @param $userId integer
     * @return string
     * */
    public static function make_code($userId){

        $str = md5(date('Ymd').$userId);

        return substr($str,0,2).substr($str,-2).str_pad($userId,5,0,STR_PAD_LEFT);
    }


    /**
     * 解析code
     * @param $code string
     * @return string
     * */
    public static function parse_code($code){

        $userId     = (int) substr($code,strlen($code)-5);

        if($code == self::make_code($userId)){

            return $userId;
        }
        return false;
    }


    public function add_quest_group(Request $request){

        $code       = $request->input('code');
        $title      = $request->input('title');
        $userId     = self::parse_code($code);
        $content    = $request->input('content');

        $category   = "英语";

        if($userId === false){

            return \API::send(2002,"code无效");
        }

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


    public function get_quest_group(Request $request){

        $userId     = $request->input('userId');
        $questList  = DB::table('quest_group')->where('user_id',$userId)->get();

        return \API::add('questGroup',$questList)->send();
    }

    /*
     *
     * */
    public function get_quest(Request $request){

        $groupId    = $request->input("groupId");
        $userId     = $request->input('userId');


        $questInfo  = DB::table('quest_list as a')
            ->leftJoin('quest_user_answer as b','b.group_id','=',DB::raw("a.quest_group_id and b.user_id = $userId and b.quest_id = a.quest_id"))
            ->where('a.quest_group_id',$groupId)
            ->whereNull('b.answer_id')
            ->orderBy('a.quest_id')
            ->select('a.*')
            ->first();

        if($questInfo)
        {
            if($questInfo->type != "JD"){

                $questInfo->answer = \GuzzleHttp\json_decode($questInfo->answer);

            }else{
                $questInfo->desc    = $questInfo->answer;
            }
        }
        return \API::add('questInfo',$questInfo)->send();
    }

    /**
     * 保存答案
     * */
    public function save_answer(Request $request){

        $questId    = $request->input("questId");
        $groupId    = $request->input('groupId');
        $answer     = $request->input('answer');
        $result     = $request->input('result');
        $userId     = $request->input('userId');

        $hasInfo    = DB::table('quest_user_answer')
            ->where('user_id',$userId)
            ->where("quest_id",$questId)
            ->where('group_id',$groupId)
            ->first();

        if($hasInfo){

            DB::table('quest_user_answer')
                ->where('answer_id',$hasInfo->answer_id)
                ->update(['result'=>$result,"answer"=>$answer]);
        }else{

            DB::table('quest_user_answer')
                ->insert([
                    'group_id'  => $groupId,
                    'quest_id'  => $questId,
                    'user_id'   => $userId,
                    'result'    => $result,
                    'answer'    => $answer,
                    'created_at'=> now()
                ]);
        }

        return \API::send();
    }


    public function results(Request $request){

        $groupId    = $request->input('groupId');
        $userId     = $request->input('userId');

        $results    = DB::table('quest_user_answer')
            ->where('user_id',$userId)
            ->where('group_id',$groupId)
            ->orderBy('quest_id')
            ->get();

        return \API::add('results',$results)->send();
    }

    /**
     * 删除历史答案进行复习
     * */
    public function review(Request $request){
        $groupId    = $request->input('groupId');
        $userId     = $request->input('userId');
        $type       = $request->input('type');
        $db = DB::table('quest_user_answer')
            ->where('user_id',$userId)
            ->where('group_id',$groupId);


        if($type == 0) //只删除错误
        {
            $db     = $db->where('result',0);
        }

        $db->delete();

        return \API::send();
    }
}