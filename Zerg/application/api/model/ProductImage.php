<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9
 * Time: 12:42
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    public $hidden = ['id','img_id','product_id','delete_time'];
    public function img(){
        return $this->hasMany('Image','id','img_id');
    }
}