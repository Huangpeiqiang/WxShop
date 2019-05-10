import {Base} from '../../util/base.js';
class My extends Base{
  constructor(){
    super();
  }
  getUserInfo(callback){
    wx.login({
      success:function(res){
        wx.getUserInfo({
          success:function(){
            typeof callback == 'function' && callback(res.userInfo);
          },
          fail:function(){
            typeof callback == 'function' && callback({
              avatarUrl: '../../images/icon/user@default.png',
              nickName: '零食商贩'
            })
          }
        })
      }
    })
  }
}

export {My};