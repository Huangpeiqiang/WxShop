<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9
 * Time: 16:23
 */

namespace app\api\controller\v1;

use app\api\model\User;
use app\api\model\UserAddress;
use app\api\service\Token as TokenService;
use app\api\validate\AddressValidate;
use app\lib\Exception\UserException;

class Address extends BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope'=>['only' => 'createOrUpdateAddress,getUserAddress']
    ];
    public function createOrUpdateAddress(){
        $validate = new AddressValidate();
        $validate->goCheck();
        //根据Token获取uid
        $uid = TokenService::getCurrentUid();
        //根据uid查询用户数据,判断用户是否存在,如果不存在抛出异常
        $user = User::get($uid);
        if (!$user){
            throw new UserException();
        }
        //获取用户从客户端提交来的地址信息,该数据经过验证层过滤uid等非法数据
        $info = $validate->getByRule(input('post.'));
        //根据用户地址信息是否存在,从而判断是添加还是更新地址
        $address = $user->address;
        if (!$address){
            $user->address()->save($info);//将$user这个表本身通过address关联函数连接在一起再插入数据
        }else{
            $user->address->save($info);
        }
    }
    public function getUserAddress(){
        $uid = TokenService::getCurrentUid();
        $userAddress = UserAddress::where("user_id","=",$uid)->find();
        if (!$userAddress){
            throw new UserException([
                'msg'=>'用户地址不存在',
                'errorCode'=>60001
            ]);
        }
        return $userAddress->toArray();
    }
}