<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/5
 * Time: 17:32
 */

namespace app\api\controller\v1;

use app\api\service\AppToken;
use app\api\service\UserToken;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\lib\Exception\TokenException;
use app\api\service\Token as TokenService;

class Token
{
    public function getToken($code = ''){
        (new TokenGet())->goCheck();
        $user = new UserToken($code);
        $token = $user->get($code);
        return [
            'token'=>$token
        ];
    }

    public function verifyToken($token){
        if (!$token){
            throw new TokenException('传入Token不许为空');
        }
        $res = TokenService::verifyToken($token);
        if (!$res){
            throw new TokenException('传入Token出现异常');
        }
    }
    public function getAppToken($ac,$se){
        (new AppTokenGet())->goCheck();
        $app = new AppToken();
        $token = $app->get($ac,$se);
        return [
            'token'=>$token
        ];
    }
}