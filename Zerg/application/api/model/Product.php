<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 11:11
 */

namespace app\api\model;


class Product extends BaseModel
{
    public $hidden = ['delete_time','category_id','from','update_time','summary','create_time','pivot',];
    public function getMainImgUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }
    public function imgs(){
        return $this->hasMany('ProductImage','product_id','id');
    }
    public function property(){
        return $this->hasMany('ProductProperty','product_id','id');
    }
    public static function getMostRecent($count){
        $products = self::limit($count)->order('create_time','desc')->select();
        return $products;
    }
    public static function getProductByCategoryID($categoryID){
        $products = self::where('category_id','=',$categoryID)->select();
        return $products;
    }
    public static function getProductDetail($id){
        $product = self::with([
            'imgs' => function($query){
                $query->with(['img'])->order('order','asc');//
            }
        ])->with('property')->find($id);
        return $product;
    }
}