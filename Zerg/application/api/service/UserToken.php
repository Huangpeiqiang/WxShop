<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6
 * Time: 9:53
 */

namespace app\api\service;


use app\api\model\User as UserModel;
use app\lib\enum\ScopeEnum;
use app\lib\Exception\TokenException;
use app\lib\Exception\WeChatException;
use think\Exception;

class UserToken extends Token
{
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;
    protected $code;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'),$this->wxAppID,$this->wxAppSecret,$this->code);
    }

    public function get()
    {
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            throw new Exception('获取Session_key或Open_ID时异常,微信内部错误');
        } else {
            $loginfail = array_key_exists('errcode', $wxResult);
            if ($loginfail) {
                throw new WeChatException([
                    'errorCode' => $wxResult['errcode'],
                    'msg' =>$wxResult['errmsg']
                ]);
            } else {
                $token = $this->grantToken($wxResult);
                return $token;
            }
        }
    }
    private function grantToken($wxResult){
        //获取openid
        $openid = $wxResult['openid'];
        //进入数据库,查看是否存在该openid
        $checkToken = UserModel::getByOpenID($openid);
        //如果存在则返回uid,不存在就新建用户
        if ($checkToken){
            $uid = $checkToken['id'];
        }else{
            $uid = $this->newUser($openid);
        }
        //生成令牌,建立缓存
        $cachedValue = $this->prepareCacheValue($wxResult,$uid);
        //将令牌返回客户端
        $token = $this->saveToCache($cachedValue);
        return $token;
    }
    private function saveToCache($cachedValue){
        $key = self::getToken();
        $value = json_encode($cachedValue);
        $result = cache($key,$value,config('setting.token_expire_in'));

        if (!$result){
            throw new TokenException();
        }
        return $key;
    }
    private function prepareCacheValue($wxResult,$uid){
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }
    private function newUser($openid){
        $user = UserModel::create([
            'openid' => $openid,
        ]);
        return $user->id;
    }
}