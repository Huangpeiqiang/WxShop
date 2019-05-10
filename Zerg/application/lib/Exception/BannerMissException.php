<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/1
 * Time: 10:48
 */

namespace app\lib\Exception;

class BannerMissException extends BaseException
{
    public $code = 404;
    public $msg = "Banner未查询得到";
    public $errorCode = 10001;
}