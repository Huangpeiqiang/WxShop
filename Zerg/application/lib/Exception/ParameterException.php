<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/30
 * Time: 11:02
 */

namespace app\lib\Exception;


class ParameterException extends BaseException
{
    public $code ="404";
    public $msg = "参数传入为空或有误";
    public $error = "100007";
}