<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/30
 * Time: 15:30
 */

namespace app\api\validate;

use app\lib\Exception\BannerMissException;
use app\lib\Exception\ParameterException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    protected function isPositiveInt($value){
        if (is_numeric($value) && is_integer($value+0) && ($value+0)>0){
            return true;
        }else{
            return false;
        }
    }

    protected function isMobile($value){
        $flag = preg_match('/^[1][3,4,5,7,8][0-9]{9}$/',$value);
        if (!$flag){
            return false;
        }else{
            return true;
        }
    }

    protected function isNotEmpty($value){
        if (empty($value)){
            return false;
        }else{
            return true;
        }
    }

    public function goCheck(){
        $param = Request::instance()->param();
        $result = $this->check($param);
            if(!$result){
                $e = new BannerMissException([
                    'msg' => $this->error
                ]);
                throw $e;
            }else{
                return true;
            }
    }

    public function getByRule($arrays){
        if (array_key_exists('uid',$arrays)||array_key_exists('user_id',$arrays)){
            throw new ParameterException([
                'msg' => '参数中含有非法的参数名user_id及uid'
            ]);
        }
        $newarray =[];
        foreach ($arrays as $key => $value){
            $newarray[$key] = $value;
        }
        return $newarray;
    }
}