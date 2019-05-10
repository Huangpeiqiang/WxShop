<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 11:25
 */

namespace app\api\validate;


class IDConnection extends BaseValidate
{
    protected $rule = [
      'ids' => 'require|checkIDs'
    ];
    protected $message = [
        'ids' => 'IDs必须是由逗号隔开的连续的正整数'
    ];
    protected function checkIDs($value=''){
        $data = explode(',',$value);
        if (empty($data)){
            return false;
        }
        foreach ($data as $id){
            if (!$this->isPositiveInt($id)){
                return false;
            }
            return true;
        }
    }
}