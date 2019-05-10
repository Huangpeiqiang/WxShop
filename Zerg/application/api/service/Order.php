<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/11
 * Time: 15:29
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\enum\OrderStatusEnum;
use app\lib\Exception\OrderException;
use think\Db;
use think\Exception;

class Order
{
    protected $oProducts;
    protected $products;
    protected $uid;

    /**
     * 生成订单总方法
     * @param $uid 用户id
     * @param $oProducts 用户需要的商品ID号和数量的集合
     * @return array 返回订单
     */
    public function place($uid,$oProducts)
    {
        //用户需求商品信息(post接收的信息)
        $this->oProducts = $oProducts;
        //根据用户需求的商品信息获取的仓库商品信息
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;
        $status = $this->getOrderStatus($oProducts);
        if (!$status['pass']){
            $status['order_id']=-1;
            return $status;
        }
        //获取订单快照
        $orderSnap = $this->snapOrder($status);
        //生成订单
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }

    /**
     * 利用order内的product_id成批获取商品详情信息
     * @param $oProducts 用户需要的商品
     * @return mixed 返回一个二维数组
     */
    private function getProductsByOrder($oProducts){
        $oPID=[];
        foreach ($oProducts as $value){
            array_push($oPID,$value['product_id']);
        }
        $products = Product::all($oPID)
            ->visible(['id','price','stock','name','main_img_url'])
            ->toArray();
        return $products;
    }

    /**
     * 生成订单快照
     * @param $status 当前订单详情
     * @return array 订单快照
     */
    private function snapOrder($status){
         $snap = [
             'orderPrice' => 0,
             'totalCount' => 0,
             'pStatus' => [],
             'snapAddress' => null,
             'snapName' => '',
             'snapImg' => ''
         ];
         $snap['orderPrice'] = $status['orderPrice'];
         $snap['totalCount'] = $status['totalCount'];
         $snap['pStatus'] = $status['pStatusArray'];
         $snap['snapAddress'] = json_encode($this->getOrderAddress());
         $snap['snapName'] = $this->products[0]['name'];
         $snap['snapImg'] = $this->products[0]['main_img_url'];
         if (count($this->products)>1){
             $snap['snapName'] .= '等';
         }
         return $snap;
    }

    /**
     * 根据uid获取订单发货地址
     * @return json 以json形式返回地址
     */
    private function getOrderAddress(){
        $address = UserAddress::where('user_id','=',$this->uid)->find();
        return $address;
    }

    /**
     * 生成订单
     * @param $snap 订单快照
     * @return array 返回订单单号,订单ID以及订单生成时间
     */
    private function createOrder($snap){
        Db::startTrans();
        try {
            $orderNo = $this->makeOrderNo();
            $order = new \app\api\model\Order();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_name = $snap['snapName'];
            $order->snap_items = json_encode($snap['pStatus']);

            $order->save();

            $orderID = $order->id;
            $createTime = $order->create_time;

            foreach ($this->oProducts as &$p) {//在oProduct内每一条添加上order_id用于查询对应order
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $createTime
            ];
        }catch (Exception $ex){
            Db::rollback();
            throw $ex;
        }
    }
    public function makeOrderNo(){
        $array = ['A','B','C','D','E','F','G'];
        $orderSn =$array[intval(date('Y'))-2017].strtoupper(dechex(date('m'))).date('d').substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
        return $orderSn;
    }

    /**
     * 根据订单号检测订单下商品是否存在并且返回订单详情
     * @param $orderID 订单号
     * @return array 返回订单状态
     * @throws OrderException
     */
    public function checkOrderStatus($orderID){
        $oProducts = OrderProduct::where('order_id','=',$orderID)->select();
        if (!$oProducts){
            throw new OrderException();
        }
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);

        $status = $this->getOrderStatus($oProducts);
        return $status;
    }

    /**
     * 获取订单状态
     * @param $oProducts 需求的商品ID和数量集合
     * @return array
     */
    private function getOrderStatus($oProducts){
        $oStatus = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatusArray' => []
        ];
        $totalCount = 0;
        $oPrice = 0;
        foreach ($oProducts as $oProduct){
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'],$oProduct['count'],$this->products
            );
            if (!$pStatus['haveStock']){
                $oStatus['pass'] = false;
            }
            $oPrice += $pStatus['totalPrice'];
            $totalCount += $pStatus['counts'];
            array_push($oStatus['pStatusArray'],$pStatus);
        }
        $oStatus['totalCount'] = $totalCount;
        $oStatus['orderPrice'] = $oPrice;
        return $oStatus;
    }

    /**
     * 获取订单内部单个商品状态
     * @param $oPID 商品ID
     * @param $oCount 商品数量
     * @param $products 商品详情库
     * @return array 从商品详情库里匹配得到的单件商品的信息
     */
    private function getProductStatus($oPID,$oCount,$products){
        $pIndex = -1;
        $status = [
            'id' => 0,
            'haveStock' => false,
            'main_img_url' => null,
            'price' => 0,
            'totalPrice' => 0,
            'counts' => 0,
            'name' => ''
        ];
            for ($i = 0;$i<count($products);$i++){
                if ($oPID == $products[$i]['id']){
                    $pIndex = $i;
                }
            }
            if ($pIndex == -1){
                throw new OrderException();
            }
            $product = $products[$pIndex];
            $status['id'] = $product['id'];
            $status['name'] = $product['name'];
            $status['counts'] = $oCount;
            $status['main_img_url'] = $product['main_img_url'];
            $status['price'] = $product['price'];
            $status['totalPrice'] = $oCount * $product['price'];
            if ($product['stock']-$oCount>=0){
                $status['haveStock'] = true;
            }
            return $status;
    }
    public function delivery($id,$jumpPage){
        $order = OrderModel::find($id);
        if (empty($order)){
            throw new OrderException();
        }
        if ($order->status != OrderStatusEnum::PAID ){
            throw new OrderException([
                'msg'=>'订单还没完成支付就想发货,想屁吃?',
                'errorCode'=>80002,
                'code'=>403
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;
        $order->save();
        $message = new DeliveryMessage();
        return $message->sendDeliverMessage($order,$jumpPage);
    }
}