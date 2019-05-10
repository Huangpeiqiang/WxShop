import {Base} from '../../util/base.js'

class Cart extends Base{
  constructor(){
    super();
    this._storageKeyName = 'cart';
  }
  /**
   * 向购物车中加入商品
   * @params item 商品列表中的信息
   * @params counts 加入商品的数量
   */
  add(item, counts) {
    var cartData = this.getCartDataFromLocal();
    if (!cartData) {
      cartData = [];
    }
    var isHadInfo = this._isHasThatOne(item.id, cartData);
    //新商品
    if (isHadInfo.index == -1) {
      var data = { 
        "id": item.id,
        'name': item.name,
        'main_img_url': item.main_img_url,
        'price': item.price
        };
      //莫名其妙item自身不能使用需要重新定义数组
      data.counts = counts;
      data.selectStatus = true;  //默认在购物车中为选中状态
      cartData.push(data);
    }
    //已有商品
    else {
      cartData[isHadInfo.index].counts += counts;
    }
    this.execSetStorageSync(cartData);  //更新本地缓存
    return cartData;
  };
  /*本地缓存 保存／更新*/
  execSetStorageSync(data) {
    wx.setStorageSync(this._storageKeyName, data);
  };
  /**
   * 
   */
  getCartTotalCounts(flag){//flag 为是否考虑选中的标志
    var cartData = this.getCartDataFromLocal();
    var totalCounts = 0;
    for(let i=0;i<cartData.length;i++){
      var item = cartData[i];
      if(flag){
        if (item.selectStatus) {
          totalCounts += item.counts;
        }
      }else{
        totalCounts += item.counts;
      }  
    }
    return totalCounts;
  }
  /**
   * @return 缓存中购物车缓存信息
   */
  getCartDataFromLocal(flag){
    var res = wx.getStorageSync(this._storageKeyName);
    if(!res){
      res = [];
    }
    //过滤未选定商品
    if(flag){
      var newRes = [];
      for (let i = 0; i < res.length; i++) {
        if (res[i].selectStatus == true) {
          newRes.push(res[i]);
        }
      }
      res = newRes
    }
    
    return res;
  }
  /**判断是否缓存内部购物车中是否存在相对应ID的商品
   * @params id 点击的商品Id号
   * @params arr 传入数组
   * @return result 返回包含index键位的数组
   */
  _isHasThatOne(id, arr){
    var item,result = { index: -1 };
    for(let i=0;i<arr.length;i++){
      item = arr[i];
      if(id == item.id){
        result={
          index:i,
          data:item
        }
        break;
      }
    }
    return result;
  }
  /**
   * 删除商品
   */
  delete(ids){
    if(!(ids instanceof Array)){
      ids = [ids];
    }
    var cartData = this.getCartDataFromLocal(this._storageKeyName);
    for (let i = 0; i < cartData.length ; i++){
      var isHasOne = this._isHasThatOne(ids[i],cartData);
      if(isHasOne){
        cartData.splice(isHasOne.index,1);
      }
    }
    this.execSetStorageSync(cartData);
  }
  /**
   * 数量修改
   */
  _changeCounts(id,count){
    var cartData = this.getCartDataFromLocal(this._storageKeyName);

    var selectInfo = this._isHasThatOne(id,cartData);
    if(selectInfo.index >=0){
      cartData[selectInfo.index].counts += count;
      if (cartData[selectInfo.index].counts<1){
        this.delete(id);
      }
    }
    this.execSetStorageSync(cartData);
  }
  /**
   * 数量增加
   */
  addCounts(id){
    var count = 1;
    this._changeCounts(id,count)
  }
  /**
   * 数量减少
   */
  cutCounts(id){
    var count = -1;
    this._changeCounts(id,count)
  }
}
export {Cart};