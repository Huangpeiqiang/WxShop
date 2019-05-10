// pages/product/product.js
import {Product} from 'product-model.js';
import {Cart} from '../cart/cart-model.js'
var product = new Product();
var cart = new Cart();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    'id':null,
    'name':null,
    'constArray': [1,2,3,4,5,6,7,8,9,10],
    'productCounts': 1,
    'productItem': ['商品详情', '产品参数', '售后保障'],
    'currentTabsIndex':0,
    'cartTotalCounts':0
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var id = options.id;
    this.data.id = id;
    this._loadData();
  },

  _loadData:function(){
    var id = this.data.id;
    product.getProductData(id,(res)=>{
      this.setData({
        'cartTotalCounts': cart.getCartTotalCounts(),
        'productDetail':res
      });
    })
  },
  binPickerChange:function(event){
    var count = event.detail.value;
    var selectedCount = this.data.constArray[count];
    this.setData({
      'productCounts': selectedCount
    });
  },
  onTabsItemTap:function(event){
    var index = product.getDataSet(event, 'index');
    this.setData({
      'currentTabsIndex':index
    })
  },
  onAddingToCartTap:function(event){
    var item = [];
    var keys = ['id', 'name', 'main_img_url', 'price'];
    for(var key in this.data.productDetail){
      if(keys.indexOf(key) >= 0){
        item[key] = this.data.productDetail[key];
      }
    }
    cart.add(item,this.data.productCounts);
    this.setData({
      'cartTotalCounts': cart.getCartTotalCounts(),
    });
  },
  toCart:function(event){
    console.log(event)
    wx.switchTab({
      url: '/pages/cart/cart',
    })
  }  
})