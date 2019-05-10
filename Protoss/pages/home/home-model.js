import {Base} from '../../util/base.js'

class Home extends Base{
  constructor(){
    super();
  }
  getBannerData(id,callBack){
   var params = {
    url:'banner/'+id,
    sCallBack:function(res){
      callBack && callBack(res.items);
    }
   };
   this.request(params);
  }

  getThemeData(id, callBack) {
    var params = {
      url: 'theme?ids=1,2,3',
      sCallBack: function (res) {
        callBack && callBack(res);
      }
    };
    this.request(params);
  }

  getProductsData(id, callBack) {
    var params = {
      url: 'product/recent',
      sCallBack: function (res) {
        callBack && callBack(res);
      }
    };
    this.request(params);
  }
}
export {Home};