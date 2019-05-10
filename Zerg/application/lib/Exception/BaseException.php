<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/1
 * Time: 10:46
 */

namespace app\lib\Exception;

use think\Exception;

class BaseException extends Exception
{
    //HTTP码 如:400,404等
    public $code=400;
    //错误返回信息
    public $msg="参数错误";
    //错误码
    public $errorCode=40000;

    public function __construct($params = [])
    {
        if (!is_array($params)){
            return ;
        }else{
            if (array_key_exists('code',$params)){
                $this->code = $params['code'];
            }
            if (array_key_exists('msg',$params)){
                $this->msg = $params['msg'];
            }
            if (array_key_exists('code',$params)){
                $this->errorCode = $params['errorCode'];
            }
        }
    }
}