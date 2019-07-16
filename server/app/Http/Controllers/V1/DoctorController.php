<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class DoctorController extends Controller
{

    public function newpet(Request $request)
    {
        $pid    = $request->input('pid');
        DB::table('doc_pet')->insertGetId(['pid'=>$pid,'created_at'=>now()]);
        return \API::send();
    }

    /**
     * 上传图片
     * */
    public function upload(Request $request)
    {
        $pid    = $request->input('pid');
        $img    = $request->file('img');

        //1.保存图片
        $img   = $img->storeAs("doctor/".$pid,time().".".$img->getClientOriginalExtension());


        //2.二值化图片
        $cmd = "activate ts &&python ".public_path("img.py") . " " . storage_path("app/".$img) . " quick";
        $cmd = str_replace("\\","/",$cmd);
        shell_exec($cmd);

        //3.保存数据库
        $imgId = DB::table("doc_img")->insertGetId(["pid"=>$pid,"img"=>$img]);

        return \API::add('imgId',$imgId)->add("img",url($img))->send();
    }

    public function delete_img(Request $request)
    {
        $id     = $request->input('imgId');
        DB::table('doc_img')->where('id',$id)->delete();
        return \API::send();
    }


}
