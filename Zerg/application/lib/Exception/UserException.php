<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10
 * Time: 11:45
 */

namespace app\lib\Exception;


class UserException extends BaseException
{
    public $code = '401';
    public $msg = '用户不存在';
    public $errorCode = '20000';
}