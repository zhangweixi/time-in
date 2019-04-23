<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Dingo\Api\Http\Request;
use App\Models\CategoryModel as Mcategory;


class CategoryController extends Controller
{

    /**
     * get the list of category
     * @param $request Request
     * @return mixed
     * */
    public function categories(Request $request){

        $pid            = $request->input('pid',0);
        $categories     = Mcategory::get_categories($pid);

        return \API::add('categories',$categories)->send();
    }
}