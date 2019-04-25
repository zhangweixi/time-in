<?php


/*
 * 递归数组获得字符串格式
 * */
function digui($array){
    $string = "";
    $obj	= new stdClass();
    if(gettype($array) == gettype($obj)){
        $array	= object_to_array($array);
    }
    foreach ($array as $key => $value) {
        if(is_array($value)) {
            $string .=digui($value);
        }else{
            $string .= $key."--------->".$value."\r\n";
        }
    }
    return $string;
}


/*
 * 打印日志
 */
if(!function_exists('mylogger')){

    function mylogger($content,$file = 'debug.txt'){

        if(is_array($content)){
            $content = digui($content);
        }

        $logContent =  "\r\n".date('Y-m-d H:i:s')."\r\n";
        $logContent .= "------------------------ BEGIN -----------------------------"."\r\n";
        $logContent .= $content."\r\n";
        $logContent .= "-------------------------  END  ----------------------------"."\r\n";
        $dir        = public_path("logs");
        $logfile    = $dir."/".$file;
        $max_size = 50000000;
        if(!is_dir($dir)){

            mkdir($dir);
        }

        if(file_exists($logfile) and (abs(filesize($logfile)) > $max_size)){
            unlink($logfile);
        }
        file_put_contents($logfile,$logContent , FILE_APPEND);
    }
}

if(function_exists('create_member_number')){
    throw new Exception('function create_member_number exists');
}else{
    /*创建会员编号*/
    function create_member_number($length = 6){
        //return date('YmdHi', time()).uniqid();
        // 密码字符集，可任意添加你需要的字符
        $chars = "0123456789";
        $password = date('YmdHi', time());
        for ( $i = 0; $i < $length; $i++ )
        {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $password;
    }
}


class API {

    private static $data = [];


    /**
     * @return api
     * @param $key string
     * @param $value string
     * */
    protected  function add($key,$value){

        self::$data[$key] = $value;

        return $this;
    }


    /**
     * @param $code integer
     * @param $msg string
     * @return mixed
     * */
    protected function send($code=200,$msg="success"){

        $tempData = ["code"=>$code,'msg'=>$msg,"data"=>self::$data];

        return response()->json($tempData);
    }


    public function __call($name, $arguments)
    {
        return $this->$name(...$arguments);
    }


    public static function __callStatic($name, $arguments)
    {
        return (new static)->$name(...$arguments);
    }
}