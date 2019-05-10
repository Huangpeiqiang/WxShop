<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 10:25
 */

namespace app\lib\Exception;


class ProductException extends BaseException
{
    public $code = '404';
    public $msg = "未查询到商品";
    public $errorCode = '20000';
}