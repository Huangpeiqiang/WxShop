<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6
 * Time: 11:19
 */

namespace app\lib\Exception;


class WeChatException extends BaseException
{
    public $code = '400';
    public $message = "申请Token参数错误";
    public $errorCode = '999';
}