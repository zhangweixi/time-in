<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Dingo\Api\Http\Request;
use App\Models\QuestionModel as Mquestion;
use App\Models\GroupModel as Mgroup;

class QuestionController extends Controller
{
    /**
     * get the list of question
     * @param $request Request
     * @return mixed
     * */
    public function questions(Request $request){

        $category   = $request->input('category',0);
        $keywords   = $request->input('keywords','');
        $questions = Mquestion::questions($category,$keywords);


        return \API::add('questions',$questions)->send();

    }

    /**
     * create a new question
     * @param $request Request
     * @return object
     * */
    public function add(Request $request){

        $category   = $request->input('category');
        $title      = $request->input('title');
        $money      = $request->input('money');
        $draft      = $request->input('draft');
        $content    = $request->input('content');

        $userId     = 1; //The ID must come from service token,can't believe client,if that,client can use any other IDs

        $question = Mquestion::create_question($userId,$title,$content,$money,$category,$draft);

        return \API::add('question',$question)->send();
    }





}