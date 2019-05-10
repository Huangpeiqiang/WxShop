<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/11
 * Time: 12:18
 */

namespace app\lib\Exception;


class FiddlenException extends BaseException
{
    public $code = '401';
    public $msg = '权限不足';
    public $errorCode = '10001';
}