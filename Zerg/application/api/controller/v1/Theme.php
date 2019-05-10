<?php

namespace app\api\controller\v1;

use app\api\validate\IDConnection;
use \app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\Exception\ThemeException;

class Theme{
    /**
     * @url /theme?ids=id1,id2,id3
     * @return 一组theme模型
     */
    public function getSimpleList($ids){
        (new IDConnection())->goCheck();
        $theme = ThemeModel::getThemeWithIDs($ids);
        if ($theme->isEmpty()){
            throw new ThemeException();
        }
        return json($theme);
    }
    public function getComplexOne($id){
        (new IDMustBePositiveInt())->goCheck();
        $products = ThemeModel::getThemeWithProducts($id);
        return $products;
    }
}
