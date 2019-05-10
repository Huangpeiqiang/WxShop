<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7
 * Time: 11:59
 */

namespace app\api\service;


use app\api\model\User;
use app\lib\Exception\OrderException;
use app\lib\Exception\UserException;

class DeliveryMessage extends WxMessage
{
    const DELIVERY_MSG_ID = 'HHPrK_cRAid4aH5TB8N6pz9q49BERhwvliU6u8WUm98';

    public function sendDeliverMessage($order,$tplJumpPage=''){
        if (!$order){
            throw new OrderException();
        }
        $this.$templateID = self::DELIVERY_MSG_ID;
        $this->formID = $order->prepay_id;
        $this->page = $tplJumpPage;
        $this->emphasisKeyword = 'keyword2.DATA';
        $this->prepareMessage($order);

        return parent::sendMessage($this->getUserOpenID($order->user_id));
    }
    private function getUserOpenID($id){
        $user = User::get($id);
        if ($user){
            throw new UserException();
        }
        return $user->openid;
    }
    private function prepareMessage($order){
        $date = new \DateTime();
        $data = [
            'keyword1'=>[
                'value'=>'韵达速递'
            ],
            'keyword2'=>[
                'value'=>$order->name,
            ],
            'keyword3'=>[
                'value'=>$order->order_no
            ],
            'keyword4'=>$date->format('Y-m-d H:i')
        ];
        $this->data = $data;
    }
}