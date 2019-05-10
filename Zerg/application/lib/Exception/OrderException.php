<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/12
 * Time: 12:35
 */

namespace app\lib\Exception;


class OrderException extends BaseException
{
    public $code = '404';
    public $msg = '查找Order数据不存在,请验证ID';
    public $errorCode = '80000';
}