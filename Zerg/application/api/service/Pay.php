<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 15:44
 */

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\lib\Exception\OrderException;
use app\lib\Exception\TokenException;
use app\api\service\Token as TokenService;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');
class Pay
{

    private $orderID;
    private $orderNo;
    public function __construct($openID)
    {
        if (!$openID){
            throw new OrderException('订单号不能为空');
        }
        $this->orderID = $openID;
    }

    public function pay(){
        $this->checkOrderValidate();//1.uid检查/订单查询不到/订单已被支付
        $order = new OrderService();
        $status = $order->checkOrderStatus($this->orderID);//1.
        if (!$status['pass']){
            return $status;
        }
        return $this->makePreOrder($status['orderPrice']);
    }

    private function makePreOrder($totalPrice){
        $openID = Token::getCurrentTokenVar('openid');
        if (!$openID){
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice*100);
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetOpenid($openID);
        $wxOrderData->SetNotify_url(config('secure.pay_notify_url'));//回调通知的地址

        return  $this->getPaySignature($wxOrderData);
    }

    private function getPaySignature($wxOrderData){
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] !='SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取支付预订单失败','error');
        }
        $this->recordPreOrder($wxOrder);

        $signature = $this->sign($wxOrder);
        return $signature;
    }

    /**
     * @param $wxOrder
     * @return array
     */
    private function sign($wxOrder){
        //封装性的优势,运用OOP修改参数值(名词参数)
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());

        $rand = md5(time().mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);

        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign = $jsApiPayData->MakeSign();
        $rawData =$jsApiPayData->GetValues();//将我们的参数转化为原始的数组
        $rawData['paySign'] = $sign;
        unset($rawData['appId']);

        return $rawData;
    }

    private function recordPreOrder($wxOrder){
        //获取prepay_id需要MCHID,然而我们么有
        //每次支付获取的prepay_id都不一样,所以必须是update
        var_dump($wxOrder);
        if(array_key_exists('return_code',$wxOrder)){
            throw new OrderException([
                'msg'=>$wxOrder['return_code'].':'.$wxOrder['return_msg']
            ]);
        }
        OrderModel::where('id','=',$this->orderID)->update([
            'prepay_id' => $wxOrder['prepay_id']
        ]);
    }

    /**
     * 检验订单状态
     * @return bool
     */
    private function checkOrderValidate(){
        //订单号无法在数据库中找到
        $order = OrderModel::find($this->orderID);
        if (!$order){
            throw new OrderException();
        }
        //订单号uid与本地uid不相同
        if (!TokenService::isValidateOperate($order->user_id)){
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => '10004'
            ]);
        }
        //订单号已支付
        if ($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
               'msg'=>'订单已被支付过了',
               'errorCode'=>'80003',
                'code'=>400
            ]);
        }
        $this->orderNo = $order->order_no;
        return true;
    }

}