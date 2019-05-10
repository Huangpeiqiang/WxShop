import {Home} from 'home-model.js';

var home = new Home();

Page({

  /**
   * 页面的初始数据
   */
  data: {

  },
onLoad:function(){
  this._loadData();
  }, 
  onReady: function () {
    wx.setNavigationBarTitle({
      title: '零食商贩'
    })
  },
_loadData:function(){
  var id = 1;
  var data = home.getBannerData(id,(res)=>{
    this.setData({
      'bannerArr':res
    });
  });
  var theme = home.getThemeData(id, (res) => {
    this.setData({
      'themeArr': res
    });
  });
  var products = home.getProductsData(id,(res)=>{
    this.setData({
      'productsArr':res
    });
  });
},
onProductItemTag:function(event){
  var id = home.getDataSet(event,'id');
  wx.navigateTo({
    url: '../product/product?id='+id,
  })
},
onThemeItemTag:function(event){
  var id = home.getDataSet(event,'id');
  var name = home.getDataSet(event,'name');
  wx.navigateTo({
    url: '../theme/theme?id='+id+'&&name='+name
  })
},

})