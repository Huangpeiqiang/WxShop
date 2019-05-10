<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 11:06
 */

namespace app\api\model;


class Category extends BaseModel
{
    public function img(){
        return $this->belongsTo('image','topic_img_id','id');
    }
}