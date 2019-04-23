<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Dingo\Api\Http\Request;
use App\Models\UserModel as MUser;


class UserController extends Controller
{

    public function login(Request $request){


    }

    /**
     * get single user detail info
     * @param $request Request
     * @return mixed
     * */
    public function detail(Request $request){

        $userId     = $request->input('userId');

        $detailInfo = MUser::detail($userId);

        return \API::add('userInfo',$detailInfo)->send();
    }
}