<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/30
 * Time: 14:34
 */

namespace app\api\validate;

class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = ["id" => "require|isPositiveInt"];
    protected $message = [
        "id" => "id必须是规则正整数"
    ];
}