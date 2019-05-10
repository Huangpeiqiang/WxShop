// pages/order/order.js
import {Cart} from '../cart/cart-model.js';
import {Order} from 'order-model.js';
import {Address} from '../../util/address.js';
var cart = new Cart();
var address = new Address();
var order = new Order();
Page({

  /**
   * 页面的初始数据
   */
  data: {
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var from = options.from;
    if (from == 'cart') {
      this._fromCart(options.account);
    }else{
      var id = options.id;
      this._fromOrder(id);
    }
  },
  _fromCart:function(account){
    var that = this;
    var productsArr = cart.getCartDataFromLocal(true);
    address.getUserAddress((res) => {
      this._bindAddress(res)
    });
    this.setData({
      'productsArr': productsArr,
      'account': account,
      'orderStatus': 0
    })
  },
  onShow() {
    if (this.data.id) {
      this._fromOrder(this.data.id);
    }
  },
  _fromOrder: function (id) {
    var that = this;
    order.getOrderInfoById(id, (data) => {
      that.setData({
        orderStatus: data.status,
        productsArr: JSON.parse(data.snap_items),
        account: data.total_price,
        basicInfo: {
          orderTime: data.create_time,
          orderNo: data.order_no
        },
      });

      // 快照地址
      var addressInfo = data.snap_address;
      addressInfo.totalDetail = address.setAddressInfo(addressInfo);
      that._bindAddress(addressInfo);
    });
  },
  /**
   * 添加/修改地址
   */
  editAddress:function(){
    var that = this;
    if(wx.chooseAddress){
      wx.chooseAddress({
        success: function (res) {
          var addressInfo = {
            'name': res.userName,
            'mobile': res.telNumber,
            'totalDetail': address.setAddressInfo(res)
          }
          that._bindAddress(addressInfo);
          address.submitAddress(res,(flag)=>{
            if(!flag){
              that.showTips('操作提示','地址信息更新失败!');
            }
          })
        },
        fail:function(res){
        }
      })
    }else{
      console.log('当前微信版本不适合chooseAdress');
    }
    
  },
  /**
   * 地址数据绑定
   */
  _bindAddress:function(addressInfo){
    this.setData({
      'addressInfo' : addressInfo
    })
  },
  /**
   * 弹窗
   */
  showTips:function(title,content,flag){
    wx.showModal({
      title: title,
      content: content,
      showCancel:false,
      success:function(res){
        if(flag){
          wx.switchTab({
            url: '/pages/my/my',
          })
        }
      }
    })
  }, 
  /**
   * 清除商品
   */
  deletePoducts() {
    var products = this.data.productsArr;
    var ids = [];
    for (let i = 0; i < products.length; i++) {
      ids.push(products[i].id);
    }
    cart.deletePoducts(ids);
  },
  /**
   * 支付
   */
  pay:function(){
    if(!this.data.addressInfo){
      this.showTips('下单提示','请填写地址信息');
    }
    if(this.data.orderStatus == 0){
      this._fristTimePay()
    }else{
      //this._oneMoreTimePay();
    }
    
  },
  /**
   * 当场支付
   */
  _fristTimePay:function(){
    //下订单
    var newOrder = []
    var products = this.data.productsArr;
    for (let i = 0; i < products.length; i++) {
      newOrder.push({
        'product_id': products[i].id,
        'count': products[i].counts
      });
    }
    var that = this;
    order.doOrder(newOrder, (res) => {
      if(res.pass){
        var id = res.order_id;
        that.data.id = res.id;
        //that.data.fromCartFlag = false;
        that._execPay(id);
      }else{
        that._orderFail(data);//下单失败
      }
    });
  },
  _execPay(id){
    order.execPay(id,(statusCode)=>{
      if(!statusCode==0){
        that.deletePoducts();
        var flag = statusCode==2;
        wx.navigateTo({
          url: '../pay-result/pay-result?id='+id+'&flag='+flag+'&from=order',
        })
      }
    });
  },
  _orderFail:function(data){
    var nameArr = [],
    name = '',
    str = '',
    pArr = data.pStatusArray;
    for(let i=0;i<pArr.length;i++){
      if(!pArr[i].haveStock){
        name = pArr[i].name;
        if(name.length>15){
          name = name.substr(0,12) + '...';
        }
        nameArr.push(name);
        if(nameArr.length >=2){
          break;
        }
      }
    }
    str += nameArr.join('、');
    if (nameArr.length >= 2) {
      str += ' 等';
    }
    str += '缺货';
    wx.showModal({
      title: '下单失败',
      content: str,
      showCancel:false,
      success:function(res){
      }
    })
  }
})