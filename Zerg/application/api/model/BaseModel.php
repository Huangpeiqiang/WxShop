<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    public function prefixImgUrl($value,$data){
        $url = $value;
        if ($data['from']==1){
            $url = config('setting.image_prefix').$value;
        }
        return $url;
    }
}
