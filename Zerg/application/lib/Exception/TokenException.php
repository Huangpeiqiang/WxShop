<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/8
 * Time: 17:11
 */

namespace app\lib\Exception;


class TokenException extends BaseException
{
    public $code = '401';
    public $msg = 'Token已过期或无效';
    public $errorCode = '10015';
}