<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 10:04
 */

namespace app\api\controller\v1;


use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\Exception\ProductException;

class Product
{
    /**
     * 获取最新商品
     * @param int $count最新商品展示数量
     * @return false|\PDOStatement|string|\think\Collection] 数据库变量
     */
    public function getRecent($count=6){
        (new Count())->goCheck();
        $products = ProductModel::getMostRecent($count);
        if ($products->isEmpty()){
            throw new ProductException();
        }
        $products = $products->hidden(['summary']);
        return $products;
    }

    /**
     * 获取分类列表
     * @param $id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAllInCategory($id){
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductByCategoryID($id);
        if ($products->isEmpty()){
            throw new ProductException();
        }
        $products = $products->hidden(['summary']);
        return $products;
    }
    public function getOne($id){
        (new IDMustBePositiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        return $product;
    }
}