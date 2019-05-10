<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/6
 * Time: 15:28
 */

namespace app\api\validate;


class AppTokenGet extends BaseValidate
{
    public $rule = [
        'ac' => 'require|isNotEmpty',
        'se' => 'require|isNotEmpty',
    ];
}