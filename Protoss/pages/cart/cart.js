// pages/cart/cart.js
import {Cart} from './cart-model.js';
var cart = new Cart();
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

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    var cartData =cart.getCartDataFromLocal();
    var cal = this._calTotalAccountAndCounts(cartData);

    this.setData({
      'cartData':cartData,
      'selectedCounts': cal.selectedCounts,
      'selectedTypeCounts':cal.selectedTypeCounts,
      'account':cal.account
    })
  },
  onHide:function(){
    cart.execSetStorageSync(this.data.cartData);
  },
  _calTotalAccountAndCounts:function(data){
    var account = 0;
    var selectedCounts = 0;
    var selectedTypeCounts = 0;

    let multiple = 100;
    for(let i=0 ; i< data.length ; i++){
      var item = data[i];
      if (item.selectStatus){
        account += item.counts * multiple * Number(item.price)  * multiple;
        selectedCounts += item.counts;
        selectedTypeCounts++;
      }
    }
    return {
      'account': account/(multiple * multiple),
      'selectedCounts':selectedCounts,
      'selectedTypeCounts':selectedTypeCounts
    };
  },
  toggleSelect:function(event){
    var id = cart.getDataSet(event, 'id');
    var index = this._getProductIndexById(id);
    var status = this.data.cartData[index].selectStatus;
    this.data.cartData[index].selectStatus = !status;
    this._resetCartData();
  },
  _getProductIndexById:function(id){
    var index = -1;
    var data = this.data.cartData;
    for(let i=0;i<data.length;i++){
      if(data[i].id==id){
        index = i;
        break;
      }
    }
    return index;
  },
  _resetCartData:function(){
    var newData = this._calTotalAccountAndCounts(this.data.cartData);
    this.setData({
      'cartData':this.data.cartData,
      'selectedCounts': newData.selectedCounts,
      'selectedTypeCounts': newData.selectedTypeCounts,
      'account': newData.account
    })
  },
  toggleSelectAll:function(event){
    var status = cart.getDataSet(event,'status') == 'true';
    var data = this.data.cartData;
    for(let i=0;i<data.length;i++){
      this.data.cartData[i].selectStatus = !status; 
    }
    this._resetCartData();
  },
  changeCounts:function(event){
    var id = cart.getDataSet(event, 'id');
    var index = this._getProductIndexById(id);
    var type = cart.getDataSet(event,'type');
    var count = 1;
    if(type == 'add'){
      cart.addCounts(id);
    }else{
      count = -1;
      cart.cutCounts(id)
    }
    this.data.cartData[index].counts += count;
    if (this.data.cartData[index].counts < 1) {
      this.data.cartData.splice(index, 1);
    }
    this._resetCartData();
  },
  delete: function (event) {
    var id = cart.getDataSet(event, 'id');
    var index = this._getProductIndexById(id);
    cart.delete(id)
    this.data.cartData.splice(index,1);
    this._resetCartData();
  },
  submitOrder:function(){
    wx.navigateTo({
      url: '/pages/order/order?account='+this.data.account+'&from=cart',
    })
  }
})