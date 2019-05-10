<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/2
 * Time: 11:01
 */

namespace app\api\model;

class Banner extends BaseModel
{
    protected $hidden = ['id','delete_time','update_time'];
    public function items(){
        return $this->hasMany('BannerItem','banner_id','id');
    }
    public static function getBannerByID($id)
    {
        $result = self::with(['items','items.img'])->find($id);
        return $result;
    }
}