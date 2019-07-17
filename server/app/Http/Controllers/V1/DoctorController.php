<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


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
        $cmd = "source activate ts &&python ".public_path("img.py") . " " . storage_path("app/".$img) . " high";
        $cmd = str_replace("\\","/",$cmd);

        shell_exec($cmd);

        //3.保存数据库
        $imgId = DB::table("doc_img")->insertGetId(["pid"=>$pid,"img"=>$img]);

        return \API::add('imgId',$imgId)
            ->add("img",config('app.url')."/storage/".$img)
            //->add('cmd',$cmd)
            ->send();
    }

    public function delete_img(Request $request)
    {
        $id     = $request->input('imgId');
        $imgInfo= DB::table('doc_img')->where('id',$id)->first();
        DB::table('doc_img')->where('id',$id)->delete();
        Storage::disk()->delete($imgInfo->img);
        return \API::send();
    }

    public function checkHasPet(Request $request){

        $pid    = $request->input('pid');
        //检查是否已存在
        $has    = DB::table('doc_pet')->where('pid',$pid)->first();

        if($has){
            return \API::send(2001,"该PET号已存在");
        }

        return \API::send();
    }

    public function submitPet(Request $request)
    {
        $pid    = $request->input('pid');
        //检查是否已存在
        $has    = DB::table('doc_pet')->where('pid',$pid)->has();

        if($has){
            return \API::send(2001,"该PET号已处理过");
        }
        //合成PDF

        //进行图片识别
        $img        = DB::table('doc_img')->where('pid',$pid)->orderBy('id')->first();
        $imgpath    = storage_path("app/".$img->img);
        $petInfo    = $this->xfyun($imgpath);
        //$petInfo    = $this->parse_result();

        if($petInfo){
            $petInfo    = implode("\n",$petInfo);
        }else{
            $petInfo    = "";
        }
        DB::table('doc_pet')->insertGetId(['pid'=>$pid,'content'=>$petInfo,"created_at"=>now()]);
        return \API::add('pid',$pid)->send();
    }

    /**
     * 讯飞手写汉字识别
     * */
    public function xfyun($imgpath){
        $daytime=strtotime('1970-1-1T00:00:00 UTC');
        // OCR手写文字识别服务webapi接口地址
        $api = "http://webapi.xfyun.cn/v1/service/v1/ocr/handwriting";
        // 应用APPID(必须为webapi类型应用,并开通手写文字识别服务,参考帖子如何创建一个webapi应用：http://bbs.xfyun.cn/forum.php?mod=viewthread&tid=36481)
        $XAppid = "5d28539f";
        // 接口密钥(webapi类型应用开通手写文字识别后，控制台--我的应用---手写文字识别---相应服务的apikey)
        $Apikey = "b988d11df1fb0d908fd4d75da3992a68";
        $XCurTime =time();
        $XParam ="";
        $XCheckSum ="";
        // 语种设置和是否返回文本位置信息
        $Param= array(
            "language"=>"cn|en",
            "location"=>"false",
        );
        // 文件上传地址
        $image=file_get_contents($imgpath);
        $image=base64_encode($image);
        $Post = array(
            'image' => $image,
        );
        $XParam = base64_encode(json_encode($Param));
        $XCheckSum = md5($Apikey.$XCurTime.$XParam);
        $headers = array();
        $headers[] = 'X-CurTime:'.$XCurTime;
        $headers[] = 'X-Param:'.$XParam;
        $headers[] = 'X-Appid:'.$XAppid;
        $headers[] = 'X-CheckSum:'.$XCheckSum;
        $headers[] = 'Content-Type:application/x-www-form-urlencoded; charset=utf-8';

        $postdata = http_build_query($Post);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => $headers,
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($api, false, $context);
        // 错误码链接：https://www.xfyun.cn/document/error-code (code返回错误码时必看)
        mylogger($result);
        return $this->parse_result($result);
    }

    /**
     * 解析讯飞的文件
     * */
    public function parse_result($str){

        //$str = file_get_contents("test.txt");
        $str        = \GuzzleHttp\json_decode($str);
        $contents   = $str->data->block[0]->line;
        $infos      = [];
        foreach ($contents as $con){
            $con = $con->word[0]->content;
            $con = str_replace("：",":",$con);
            $info = explode(":",$con);
            if(count($info) == 2 && $info[1] != ""){
                array_push($infos,$con);
            }
        }
        return $infos;
    }

    /**
     * 预览单个文件
     * */
    public function preview(Request $request)
    {
        $pid    = $request->input('pid');
        $imgs   = DB::table('doc_img')->where('pid',$pid)->select()->get();
        foreach($imgs as $img){
            $img->img = config("app.url")."/storage/".$img->img;
            //$img->img = config("app.url")."/".$img->img;
        }
        return view('doctor/pdf',["imgs"=>$imgs]);
    }
}
