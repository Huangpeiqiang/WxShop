<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/8
 * Time: 16:25
 */

namespace app\api\service;


use app\lib\Exception\TokenException;
use app\lib\Exception\UserException;
use think\Cache;
use think\Exception;
use think\Request;
use app\lib\enum\ScopeEnum;

class Token
{
    public static function getToken(){
        //由三段字符连接组成
        $randChars = getRandChars(32);
        $timesamp = $_SERVER['REQUEST_TIME'];
        $salt = config('secure.token_salt');

        return md5($randChars.$timesamp.$salt);
    }
    /*
     * 检查权限
     */
    public static function needPrimaryScope()    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            }
            else{
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    // 用户专有权限
    public static function needExclusiveScope()    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope){
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    public static function needSuperScope()    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope){
            if ($scope == ScopeEnum::Super) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }
    /*
     * 获取用户数据
     */
    public static function getCurrentTokenVar($key){
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars){
            throw new TokenException();
        }else{
            if (!is_array($vars)){
                $vars = json_decode($vars,true);
            }
            if (!array_key_exists($key,$vars)){
                throw new Exception('尝试获取的Token并不存在');
            }else{
                return $vars[$key];
            }
        }
    }
    public static function getCurrentUid(){
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }
    public static function isValidateOperate($checkUID){
        if (!$checkUID){
            throw new Exception('检测uid是否合法需要传入uid');
        }
        $uid = self::getCurrentUid();
        if ($uid!=$checkUID){
            throw new UserException('用户ID不相同,有错误');
        }
        return true;
    }
    public static function verifyToken($token){
        $exist = Cache::get($token);
        if ($exist){
            return true;
        }else{
            return false;
        }
    }
}