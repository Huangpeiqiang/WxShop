<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9
 * Time: 12:27
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{
    public $hidden = ['id','delete_time','update_time','product_id'];
}