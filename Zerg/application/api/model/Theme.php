<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 11:12
 */

namespace app\api\model;


class Theme extends BaseModel
{
    protected $hidden = ['delete_time','head_img_id','topic_img_id','update_time'];
    public function topicImg(){
        return $this->belongsTo('Image','topic_img_id','id');
    }
    public function headImg(){
        return $this->belongsTo('Image','head_img_id','id');
    }
    public function products(){
        return $this->belongsToMany('product','theme_product','product_id','theme_id');
    }
    public static function getThemeWithIDs($ids){
        $data = explode(',',$ids);
        $theme = self::with('topicImg,headImg')->select($data);
        return $theme;
    }
    public static function getThemeWithProducts($id){
        $products = self::with('products,topicImg,headImg')->find($id);
        return $products;
    }
}