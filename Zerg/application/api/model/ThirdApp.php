<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/6
 * Time: 15:33
 */

namespace app\api\model;


class ThirdApp extends BaseModel
{
    public static function check($ac,$sc){
        $user = self::where('app_id','=',$ac)->where('app_secret','=',$sc)->find();
        return $user;
    }
}