<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7
 * Time: 11:05
 */

namespace app\api\service;


use think\Exception;

class AccessToken
{
    private $accessUrl;
    const TOKEN_CACHED_KEY = 'access';
    //确保access_token在有效时间内,cache设定值就必须小于access_token的生命周期(7200)
    const TOKEN_EXPIRE_IN = 7000;
    function __construct()
    {
        $url = config('wx.accessGetUrl');
        $url = sprintf($url,config('wx.app_id'),config('wx.app_secret'));
        $this->accessUrl = $url;
    }

    public function get(){
        $token = $this->getFromCache();
        if (!$token){
            $this->getFromWxServer();
        }else{
            return $token;
        }
    }

    private function getFromWxServer(){
        $token = curl_get($this->accessUrl);
        $token = json_decode($token,true);
        if (!$token){
            throw new Exception('获取access_token异常');
        }
        if (!empty($token['errcode'])){
            throw new Exception($token['errmsg']);
        }
        $this.saveTocache($token);
        return $token;
    }

    private function saveTocache($value){
        $flag = cache(TOKEN_CACHED_KEY,$value['access_token'],TOKEN_EXPIRE_IN);
        if (!$flag) {
            throw new Exception('缓存失败');
        }
    }

    private function getFromCache(){
        $token = cache('TOKEN_CACHED_KEY');
        return $token;
    }
}