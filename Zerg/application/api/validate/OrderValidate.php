<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/11
 * Time: 14:45
 */

namespace app\api\validate;


use app\lib\Exception\ParameterException;
use app\lib\Exception\ProductException;

class OrderValidate extends BaseValidate
{
    public $rule = [
        'products' => 'checkProducts'
    ];
    public $singleRule = [
        'product_id' => 'require|isPositiveInt',
        'count' => 'require|isPositiveInt'
    ];
    protected function checkProducts($values){
        if (empty($values)){
            throw new ProductException([
                'msg'=>'传入参数为空'
            ]);
        }
        if (!is_array($values)){
            throw new ProductException([
                'msg'=>'传入参数类型有误'
            ]);
        }
        foreach ($values as $value){
            $this->checkProduct($value);
        }
        return true;
    }
    protected function checkProduct($value){
        $validate = new BaseValidate($this->singleRule);//利用OOP调用基类函数
        $res = $validate->check($value);
        if (!$res){
            throw new ParameterException([
                'msg'=>'数组内单位参数输入有误'
            ]);
        }
        return true;
    }
}