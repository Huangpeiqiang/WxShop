<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/30
 * Time: 10:56
 */

namespace app\api\controller\v1;

use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\Exception\BannerMissException;


class Banner
{
    /* *
     * 获取bannner的id号
     * @url /banner/:id
     * @http GET
     */
    public function  getBanner($id){

        $validate = new IDMustBePositiveInt();
        $validate->goCheck();
        $banner = BannerModel::getBannerByID($id);
        if (!$banner) {
            throw new BannerMissException();
        }
        return  $banner;
    }
}