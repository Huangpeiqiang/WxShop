import {Config} from 'config.js'
class Token{
  constructor(){
    this.tokenUrl = Config.restUrl + 'token/user';
    this.verifyUrl = Config.restUrl + 'token/verify';
  }
  verify(){
    var token = wx.getStorageSync('token');
    if(!token){
      this.getTokenfromService();
    }else{
      this._verifyFromService(token);
    }
  }
  /**
   * 
   */
  getTokenfromService(callback){
    var that = this;
    wx.login({
      success:function(res){
        wx.request({
          url: that.tokenUrl,
          method:'POST',
          data:{
            'code':res.code
          },
          success:function(res){
            wx.setStorageSync('token', res.data.token);
            callback && callback(res.data.token);
          }
        })
      }
    })
  }
  /**
   * 
   */
  _verifyFromService(){
    var that = this;
    wx.request({
      url: that.verifyUrl,
      method: 'POST',
      data:{
        'token':wx.getStorageSync('token')
      },
      success:function(res){
        var valid = res.data.isvalid;
        if(!valid){
          that.getTokenfromService();
        }
      }
    })
  }
}

export {Token};