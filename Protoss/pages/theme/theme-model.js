 import {Base} from '../../util/base.js'

class Theme extends Base{
  constructor(){
    super();
  }
  getThemeData(id,callBack){
    var params = {
      url: 'theme/' + id,
      sCallBack: function (res) {
        callBack && callBack(res);
      }
    };
    this.request(params);
  }
}

export {Theme};