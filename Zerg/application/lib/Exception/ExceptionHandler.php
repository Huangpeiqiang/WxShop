<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/1
 * Time: 10:53
 */

namespace app\lib\Exception;

use think\exception\Handle;
use think\Log;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    public function render(\Exception $e)
    {
        if ($e instanceof BaseException){
           $this->code = $e->code;
           $this->msg = $e->msg;
           $this->errorCode = $e->errorCode;
       }else{
            if (Config('app_debug')){
                return parent::render($e);
            }else{
                $this->code = '500';
                $this->msg = "服务器错误";
                $this->errorCode = '999';
                $this->recordErrorLog($e);
            }
        }
        $info = array(
        'errorcode'=>$this->errorCode,
        'msg'=>$this->msg
    );
        return json($info,$this->code);
    }

    private function recordErrorLog(\Exception $e){
        Log::init([
            "type" => 'File',
            "level" => ['error']//必须是数组
        ]);
        Log::record($e->getMessage(),'error');
    }
}