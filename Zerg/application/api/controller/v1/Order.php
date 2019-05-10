<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/11
 * Time: 15:07
 */

namespace app\api\controller\v1;

use app\api\service\Token;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderValidate;
use app\api\validate\PagingParameter;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\lib\Exception\OrderException;

class Order extends BaseController
{
    public $beforeActionList = [
        'checkPrimaryScope'=>['only'=>'placeOrder'],
        'checkPrimaryScope'=>['only'=>'getSummaryByUser']
    ];

    /**
     * @param int $page 当前页
     * @param int $size 每页显示数量
     * @return array|\think\response\Json
     */
    public function getSummaryByUser($page = 1,$size=15){
        (new PagingParameter())->goCheck();
        $uid = Token::getCurrentUid();
        $paging = OrderModel::getSummaryByUser($uid,$page,$size);
        if ($paging->isEmpty()){
            $data=[];
        }
        $data = $paging->toArray();
        return [
            'data' => $data,
            'current_page' => $paging->getCurrentPage()
        ];
    }

    /**
     * 根据订单ID获取单个订单信息
     * @param $id 订单ID
     * @return OrderModel
     */
    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();
        $order = OrderModel::get($id);
        if (empty($order)){
            throw new OrderException();
        }
        return $order;
    }

    /**
     * @return array 订单状态
     * @throws \app\lib\Exception\BannerMissException
     */
    public function placeOrder(){
        (new OrderValidate())->goCheck();
        $oProducts = input('post.products/a');
        $uid = Token::getCurrentUid();
        $order = new \app\api\service\Order();
        $status = $order->place($uid,$oProducts);
        return $status;
    }

    public function getSummary($page = 1,$size=20){
        (new PagingParameter())->goCheck();
        $page = OrderModel::getSummary($page,$size);
        if ($page->isEmpty()){
            $data = [];
        }
        $data=$page->hidden(['snap_address','snap_items'])->toArray();
        return [
            'data'=>$data,
            'current_page'=>$page->getCurrentPage()
        ];
    }
    public function delivery($id){
        (new IDMustBePositiveInt())->goCheck();
        $order = new OrderService();
        $flag = $order->delivery($id);
        if($flag){
            return new SuccessMessage();
        }
    }
}