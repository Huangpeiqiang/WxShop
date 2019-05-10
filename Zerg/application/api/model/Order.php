<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/16
 * Time: 12:22
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['delete_time','update_time'];
    protected $autoWriteTimestamp = true;

    public function getSnapAddressAttr($value){
        if (empty($value)){
            return null;
        }
        return json_decode($value);
    }
    public function getSnapItems($value){
        if (empty($value)){
            return null;
        }
        return json_decode($value);
    }
    public static function getSummaryByUser($uid,$page = 1,$size = 15){
        $paging = self::where('user_id','=',$uid)->order('create_time desc')
            ->paginate($size,true,[
                'page' => $page
            ]);
        return $paging;
    }
    public static function getSummary($page = 1,$size = 20){
        $paging = self::order('create_time desc')->paginate($size,true,[
            'page'=>$page
        ]);
        return $paging;
    }
}