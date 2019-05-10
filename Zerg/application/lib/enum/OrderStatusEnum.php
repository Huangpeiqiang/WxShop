<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 16:55
 */

namespace app\lib\enum;


class OrderStatusEnum
{
    //待支付
    const UNPAID = 1;
    //已支付
    const PAID = 2;
    //已发货
    const DELIVERED = 3;
    //支付但库存不足
    const PAIN_BUT_OUT_OF = 4;
}