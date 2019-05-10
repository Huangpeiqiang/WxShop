<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 15:41
 */

namespace app\api\controller\v1;

use \app\api\service\Pay as PayService;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only'=>'getPreOrder']
    ];

    public function getPreOrder($id = ''){
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    public function receiveNotify(){
        //通知频率15/15/30/180/1800/1800/1800/1800/3600, 单位:秒
        $notify = new WxNotify();
        $notify->Handle();
    }
}