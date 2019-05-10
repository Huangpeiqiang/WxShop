<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6
 * Time: 9:46
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        "code" => "require|isNotEmpty"
    ];
    protected $message = [
        "code" => "code不允许为空"
    ];
}