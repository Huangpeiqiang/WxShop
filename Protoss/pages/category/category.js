// pages/category/category.js
import {Category} from './category-model.js';
var cat = new Category();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    'currentTap':0
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this._loadData();
  },
  _loadData:function(){
    cat.getCategoryType((res)=>{
      this.setData({
        'categoryType':res
      });
      //微信小程序为异步调用,确保categoryType被调用再执行下一步
      cat.getProductsByCategory(res[0].id, (data) => {
        var obj = {
          procucts:data,
          topImgUrl:res[0].img.url,
          title:res[0].name
        };
        this.setData({
          'categoryProducts': obj
        })
      })
    })
  },
  onCategoryTap:function(event){
    var index = cat.getDataSet(event,'index');
    this.setData({
      'currentTap':index
    })
    cat.getCategoryType((res)=>{
      var category = res[index];
      cat.getProductsByCategory(category.id, (data) => {
        var obj = {
          procucts: data,
          topImgUrl: category.img.url,
          title: category.name
        };
        this.setData({
          'categoryProducts': obj
        })
      })
    });
  },
  onProductsItemTap: function (event) {
    var id = cat.getDataSet(event, 'id');
    wx.navigateTo({
      url: '../product/product?id=' + id
    })
  }
  
})