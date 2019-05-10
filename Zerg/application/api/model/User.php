<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 17:32
 */

namespace app\api\model;


class User extends BaseModel
{
    public function address(){
        return $this->hasOne('UserAddress','user_id','id');
    }
    public static function getByOpenID($openid){
        $res = self::where('openid','=',$openid)->find();
        return $res;
    }
}