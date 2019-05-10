<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

Route::get('api/v1/banner/:id','api/v1.Banner/getBanner');

Route::get('api/v1/theme','api/v1.Theme/getSimpleList');
Route::get('api/v1/theme/:id','api/v1.Theme/getComplexOne');

Route::get('api/v1/category','api/v1.Category/getAllCategory');

Route::group('api/v1/product',function (){
    Route::get('/by_category/:id','api/v1.Product/getAllInCategory');
    Route::get('/:id','api/v1.Product/getOne',[],['id'=>'\d+']);
    Route::get('/recent','api/v1.Product/getRecent');
});

Route::post('api/v1/address','api/v1.Address/createOrUpdateAddress');
Route::get('api/v1/address','api/v1.Address/getUserAddress');

Route::group('api/v1/token',function (){
    Route::post('/user','api/v1.Token/getToken');
    Route::post('/verify','api/v1.Token/verifyToken');
    Route::post('/app','api/v1.Token/getAppToken');
});

Route::post('api/v1/order','api/v1.Order/placeOrder');
Route::group('api/v1/order',function (){
    Route::get('/by_user','api/v1.Order/getSummaryByUser');
    Route::get('/paginate','api/v1.Order/getSummary');
    Route::get('/:id','api/v1.Order/getDetail');
    Route::get('/delivery','api/v1.Order/delivery');

});

Route::post('api/v1/pay/pre_order','api/v1.Pay/getPreOrder');
Route::post('api/v1/pay/notify','api/v1.Pay/receiveNotify');

