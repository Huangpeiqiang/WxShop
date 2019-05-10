<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 14:04
 */

namespace app\lib\Exception;


class ThemeException extends BaseException
{
    public $code = "404";
    public $msg = "未寻找到对应内容";
    public $errorCode = "100000";
}