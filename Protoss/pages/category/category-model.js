import {Base} from '../../util/base.js'
class Category extends Base{
  constructor(){
    super();
  }
  getCategoryType(callBack){
    var params = {
      url:'category',
      sCallBack:function(res){
        callBack && callBack(res);
      }
    }
    this.request(params);
  }
  getProductsByCategory(id, callBack) {
    var params = {
      url: 'product/by_category/' + id,
      sCallBack: function (res) {
        callBack && callBack(res);
      }
    }
    this.request(params);
  }
}
export {Category};