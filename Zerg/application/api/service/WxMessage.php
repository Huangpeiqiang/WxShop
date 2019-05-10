<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7
 * Time: 12:04
 */

namespace app\api\service;

class WxMessage
{
    private $sendUrl = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=%s';
    private $touser;
    private $color;

    protected $templateID;
    protected $page;
    protected $formID;
    protected $data;
    protected $emphasisKeyword;
    function __construct()
    {
        $accessToken = new AccessToken();
        $token = $accessToken->get();
        $this->sendUrl = sprintf($this->sendUrl,$token);
    }
    protected function sendMessage($openID){
        $data = [
            'touser'=>$openID,
            'template_id'=>$this->templateID,
            'form_id'=>$this->formID,
            'data'=>$this->data,
            'color'=>$this->color,
            'emphasis_keyword'=>$this->emphasisKeyword
        ];
        $result = curl_post($this.$this->sendUrl,$data);
        $result = json_decode($result,true);
        if ($result['errcode']==0){
            return true;
        }
    }
}