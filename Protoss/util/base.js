import {Config} from '../util/config.js';
import {Token} from 'token.js';
var token = new Token();
class Base{

  constructor(){
    this.baseRequestUrl = Config.restUrl;
  }

  request(params,noFetch){
    var url = this.baseRequestUrl+params.url;
    if(!params.type){
      params.type = 'GET';
    }
    wx.request({
      url: url,
      data: params.data,
      method: params.type,
      header: {
        'Content-type':'application/json',
        'token':wx.getStorageSync('token')
      },
      success:function(res){
        var code = res.statusCode.toString();
        var fristCode = code.charAt(0);
        if(fristCode == '2'){
          params.sCallBack && params.sCallBack(res.data);
        }else{
          if(code == 401){
            if(!noFetch){
              this._refetch(params);
            }
          } 
          if (noFetch) {
            params.eCallBack && params.eCallBack(res.data);
          }
        }
      },
      fail:function(err){
        console.log(err);
      }
    })
  }
  _refetch(params){
    token.getTokenfromService((token)=>{
      this.request(params,true);
    });
  }
  getDataSet(event,key){
    return event.currentTarget.dataset[key];
  }
}

export {Base};