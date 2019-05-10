<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 11:15
 */

namespace app\lib\Exception;


class CategoryException extends BaseException
{
    public $code = '404';
    public $msg = '未查询得到栏目信息';
    public $errorCode = '30000';
}