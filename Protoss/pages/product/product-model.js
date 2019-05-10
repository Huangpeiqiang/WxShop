import {Base} from '../../util/base.js'

class Product extends Base{
  constructor(){
    super();
  }
  getProductData(id,callBack){
    var params = {
      url:'product/'+id,
      sCallBack: function (res) {
        callBack && callBack(res);
      }
    };
    this.request(params);
  }
  write(res){
    console.log(res);
  }
}
export {Product};