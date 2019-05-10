<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 11:01
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;

class Category
{
    public function getAllCategory(){
        $categorys = CategoryModel::with('img')->select();
        if ($categorys->isEmpty()){
            throw new CategoryException();
        }
        return $categorys;
    }
}