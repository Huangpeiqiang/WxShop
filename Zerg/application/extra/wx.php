<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/6
 * Time: 10:05
 */
return [
    'app_id' => 'wxf01d13c745bed7b8',
    'app_secret' => 'c3a6b7306d08ea6e85fe62022c7391ab',
    'login_url' => 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
    //获取access_token的地址
    'accessGetUrl' => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s'
];