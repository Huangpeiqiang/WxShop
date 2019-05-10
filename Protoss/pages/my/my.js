// pages/my/my.js
import { My } from './my-model.js';
import { Address } from '../../util/address.js';
import { Order } from '../order/order-model.js';
var my = new My();  
var address = new Address();
var order = new Order();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    'addressInfo':null,
    'pageIndex':1,
    'orderArr':[],
    'isLoadedAll':false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this._loadData();
  },
  onShow:function(){
    var flag = order.hasNewOrder();
    if(flag){
      this._refresh();
    }
  },
  onReachBottom:function(){
    if(!this.data.isLoadedAll){
      this.data.pageIndex++;
      this._getOrders();
    }
  },
  _loadData:function(){
    var that = this;
    address.getUserAddress((data)=>{
      that._bindAddressInfo(data);
    }); 
    this._getOrders();
  },
  editAddress: function () {
    var that = this;
    wx.chooseAddress({
      success:function(res){
        var addressInfo = {
          name:res.userName,
          mobile:res.telNumber,
          totalDetail: address.setAddressInfo(res)
        }
        that._bindAddressInfo(addressInfo);
      }
    });
  },
  _bindAddressInfo(res){
    this.setData({
      'addressInfo': res
    })
  },
  _getOrders: function (callback) {
    var that = this;
    order.getSummaryByUser(this.data.pageIndex,(res)=>{
      var data = res.data;
      if(data.data.length > 0){
        this.data.orderArr.push.apply(this.data.orderArr,data.data);//.push.apply为数组合并,内部需要两个参数
        that.setData({
          'orderArr':this.data.orderArr
        })
      } else {
        that.data.isLoadedAll = true;  //已经全部加载完毕
        that.data.pageIndex = 1;
      }
      callback && callback();
    })
  },
  showOrderDetailInfo:function(event){
    var id = my.getDataSet(event, 'id');
    wx.navigateTo({
      url: '../order/order?id='+id+'&&from=my'
    })
  },
  rePay:function(event){
    var id = my.getDataSet(event,'id');
    var index = my.getDataSet(event, 'index');
    this._execPay(id,index);
  },
  _execPay:function(id,index){
    var that = this;
    order.execPay(id,(statusCode)=>{
      var statusCode = data;
      if (statusCode>0){
        var flag = statusCode == 2;
        if(flag){
          that.data.orderArr[index].status = 2;
          that.setData({
            orderArr:that.data.orderArr
          })
        wx.navigateTo({
          url:'../pay-result/pay-result?id='+id+'&&flag='+flag+'&&from=my'
        })
        }else{
          that.showTip('操作消息','申请支付失败');
        }
      }
    })
  },
  showTip:function(title,content){
    wx.showModal({
      title: title,
      content: content,
      showCancel: false
    })
  },
  _refresh: function () {
    var that = this;
    this.data.orderArr = [];
    this._getOrders((data)=>{
      this.data.isLoadedAll = false;
      this.data.pageIndex = 1;
      order.execSetStorageSync(false);
    })
  }
})