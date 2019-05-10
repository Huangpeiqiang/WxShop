<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 15:59
 */

namespace app\api\service;

use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS'){
            $orderNo = $data['out_trade_no'];
            Db::startTrans();//利用事务防止多次调用导致的函数重复调用
            try{
                $order = OrderModel::where('order_no','=',$orderNo)
                    ->lock(true)
                    ->find();
                if ($order->status == 1){
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStatus($order->id);
                    if ($stockStatus['pass']){//当高并发情况导致以上内容执行过慢,会导致status内容来不及改变,从而调用两次reducestock
                        $this->updateOrderStatus($order->id,true);
                        $this->reduceStock($stockStatus);
                    }else{
                        $this->updateOrderStatus($order->id,false);
                    }
                    Db::commit();
                    return true;//用来控制通知频率,true则不会继续调用
                }
            }catch (Exception $ex){
                Db::rollback();
                Log::error($ex);
                return false;
            }
        }else{
            return true;
        }
    }
    private function updateOrderStatus($orderID,$success){
        $status = $success?
            OrderStatusEnum::PAID:
            OrderStatusEnum::PAIN_BUT_OUT_OF;
        OrderModel::where('order_id','=',$orderID)->update([
                'status' => $status
        ]);
    }
    private function reduceStock($stockStatus){
        foreach ($stockStatus['pStatusArray'] as $singlePStatus){
            Product::where('id','=',$singlePStatus['id'])->
            setDec('stock',$singlePStatus['count']);
        }
    }
}