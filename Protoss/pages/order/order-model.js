import {Base} from '../../util/base.js';

class Order extends Base{
  constructor(){
    super();
    this._storageKeyName = 'newOrder'
  }
  /**
   * 请求订单号
   */
  doOrder(products,callback){
    var that = this;
    var params = {
      url: 'order',
      type:'post',
      data: {'products': products},
      sCallBack:function(res){
        that.execSetStorageSync(true);
        callback && callback(res);
      }
    }
    this.request(params);
  }
  /**
   * 加入缓存
   */
  execSetStorageSync(data) {
    wx.setStorageSync(this._storageKeyName, data)
  }
  /**
   * 发起支付
   */
  execPay(orderNum, callback) {
    var params = {
      url:'pay/pre_order',
      type:'post',
      data:{id:orderNum},
      success:function(data){
        var timeStamp = data.timeStamp;
        if (timeSamp) {
          wx.requestPayment({
            timeStamp: timeStamp.toString(),
            nonceStr: data.nonceStr,
            package: data.package,
            signType: data.signType,
            paySign: data.paySign,
            success: function (res) {
              //支付成功 1
              callback && callback(2);
            },
            fail: function (res) {
              //支付失败 2
              callback && callback(1);
            }
          })
          this.request(params);
        } else {
          //无法进行支付 0
          callback && callback(0);
        }
      },
      fail:function(res){
        console.log(res);
      }
    }
    this.request(params);
  }
  /**
   * 获取一条订单信息
   */
  getOrderInfoById(id,callback){
    var params = {
      url:'order/'+id,
      sCallBack:function(res){
        callback && callback(res)
      }
    }
    this.request(params);
  }
  /**
   * 获取用户所有订单
   */
  getSummaryByUser(pageIndex,callback){
    var params = {
      url:'order/by_user',
      type:'get',
      data:{page:pageIndex},
      sCallBack: function (res) {
        callback && callback(res);
      }
    }; 
    this.request(params);
  }
  /**
   * 检查是否出现新订单
   */
  hasNewOrder(){
    var flag = wx.getStorageSync(this._storageKeyName);
    return flag;
  }
}
  

export {Order};