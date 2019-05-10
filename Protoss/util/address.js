import {Base} from 'base.js'
import {Config} from 'config.js'

class Address extends Base{
  constructor(){
    super();
  }
  /**
   * 使客户端参数与数据库相匹配
   */
  setAddressInfo(res){
    var province = res.provinceName || res.province;
    var city = res.cityName || res.city;
    var country = res.countyName || res.country;
    var detail = res.detailInfo || res.detail;

    var totalDetail =  province + city + country + detail;

    if (this._isCenterCity(city)){
      totalDetail = city + country + detail;
    }

    return totalDetail;
  }
  /**
   * 验证直辖市
   */
  _isCenterCity(name){
    var citys=['北京市','上海市','重庆市','天津市']
    if(citys.indexOf(name)>=0){
      return true;
    }
  }
  /**
   * 获取我的地址
   */
  getUserAddress(callback){
    var that = this;
    var params = {
      url: 'address',
      sCallBack: function (res) {
        if (res) {
          res.totalDetail = that.setAddressInfo(res);
          callback && callback(res);
        }
      }
    };
    this.request(params);
  }
  /*更新保存地址*/
  submitAddress(data, callback) {
    data = this._setUpAddress(data);
    var param = {
      url: 'address',
      type: 'POST',
      data: data,
      sCallback: function (res) {
        callback && callback(true, res);
      }, eCallback(res) {
        callback && callback(false, res);
      }
    };
    this.request(param);
  }
  /*保存地址*/
  _setUpAddress(res, callback) {
    var formData = {
      name: res.userName,
      province: res.provinceName,
      city: res.cityName,
      country: res.countyName,
      mobile: res.telNumber,
      detail: res.detailInfo
    };
    return formData;
  }
}

export {Address};