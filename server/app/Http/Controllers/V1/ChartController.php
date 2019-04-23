<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Dingo\Api\Http\Request;
use App\Models\GroupModel as Mgroup;

class ChartController extends Controller
{


    /**
     * if someone want to wanswer the question,the system must ceate a group bewteen answer and creater before chart
     * @param $request Request
     * */
    public function create_group(Request $request){

        $questionId     = $request->input('questionId');
        $answerUserId   = 2;
        $createrUserId  = 1;


        /*
         * it must be check whether the chart already exists
         * there is one problem will bring some error which if answer user creat the group but say nothing,
         * create user app will show the group  info but answer user no say any thing
         * if want the group is valid ,the total number of message must more than one
         * */

        $groupId        = Mgroup::create_group($questionId,$answerUserId,$createrUserId);

        return \API::add('groupId',$groupId)->send();
    }

    /**
     * get all group which talked whth quetion creater
     * @param $request Request
     * @return object
     * */
    public function groups(Request $request){

        $questionId = $request->input('questionId',0);

        $groups     = Mgroup::groups($questionId);

        return \API::add('groups',$groups)->send();
    }
}