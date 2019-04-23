<?php

/**
 * @property api1 instance
 * */
class API {

    private static $data = [];

    private static $instance ;


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