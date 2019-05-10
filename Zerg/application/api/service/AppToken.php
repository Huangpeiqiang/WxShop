<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/6
 * Time: 15:31
 */

namespace app\api\service;

use app\api\model\ThirdApp;
use app\lib\Exception\TokenException;

class AppToken extends Token
{
    public function get($ac,$se){
        $user = ThirdApp::check($ac,$se);
        if (!$user){
            throw new TokenException();
        }else{
            $scope = $user->scope;
            $uid = $user->id;
            $value = [
                'uid'=>$uid,
                'scope'=>$scope
            ];
            $token = $this->saveToCache($value);
            return $token;
        }
    }
    private function saveToCache($cachedValue){
        $key = self::getToken();
        $value = json_encode($cachedValue);
        $result = cache($key,$value,config('setting.token_expire_in'));

        if (!$result){
            throw new TokenException([
                'msg'=>'服务器缓存异常',
                'errorCode'=>10005
            ]);
        }
        return $key;
    }
}