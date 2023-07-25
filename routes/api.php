<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');

    Route::get('showvehiclenumberinsales', 'Api\PurchaseOrderController@showVehicleNumberinSales');

    Route::get('salesorder/{id}', 'Api\SalesOrderController@show');
    Route::post('salesorder', 'Api\SalesOrderController@store');
    Route::put('salesorder/{id}', 'Api\SalesOrderController@update');
    Route::delete('salesorder/{id}', 'Api\SalesOrderController@destroy');
    Route::get('salesorder', 'Api\SalesOrderController@index');
    Route::get('showagreementnumber', 'Api\SalesOrderController@showAgreementNumber');

    Route::get('showagreementnumberinrehiring', 'Api\SalesOrderController@showAgreementNumberInRehiring');
    
    Route::get('purchaseorder/{id}', 'Api\PurchaseOrderController@show');
    Route::post('purchaseorder', 'Api\PurchaseOrderController@store');
    Route::put('purchaseorder/{id}', 'Api\PurchaseOrderController@update');
    Route::delete('purchaseorder/{id}', 'Api\PurchaseOrderController@destroy');
    Route::get('purchaseorder', 'Api\PurchaseOrderController@index');
    Route::get('purchaseorderall', 'Api\PurchaseOrderController@indexAll');
    Route::get('listvehicleinvehiclecard/{id}', 'Api\PurchaseOrderController@listVehicleInVehicleCard');
    
    Route::get('compilationdb', 'Api\PurchaseOrderController@compilationDB');
    
    Route::get('showvehicle', 'Api\PurchaseOrderController@showVehicle');
    Route::get('listvehiclebyid/{id}', 'Api\PurchaseOrderController@listVehicleById');
    
    Route::get('showvehiclenumber', 'Api\PurchaseOrderController@showVehicleNumber');
    Route::get('showvehiclenumberinothercost', 'Api\PurchaseOrderController@showVehicleNumberInOtherCost');
    Route::get('showvehiclenumberinotherincome', 'Api\PurchaseOrderController@showVehicleNumberInOtherIncome');
    
    Route::get('rehiringorder/{id}', 'Api\RehiringController@show');
    Route::post('rehiringorder', 'Api\RehiringController@store');
    Route::put('rehiringorder/{id}', 'Api\RehiringController@update');
    Route::delete('rehiringorder/{id}', 'Api\RehiringController@destroy');
    Route::get('rehiringorder', 'Api\RehiringController@index');
    Route::get('showvehiclesold', 'Api\RehiringController@showVehicleSold');
    Route::put('updatevehiclesold/{id}', 'Api\RehiringController@updateVehicleSold');

    Route::get('showvehiclerehiringorder', 'Api\RehiringController@showVehicleRehiringOrder');
    
    Route::get('othercost/{id}', 'Api\OtherCostController@show');
    Route::post('othercost', 'Api\OtherCostController@store');
    Route::put('othercost/{id}', 'Api\OtherCostController@update');
    Route::delete('othercost/{id}', 'Api\OtherCostController@destroy');
    Route::get('othercost', 'Api\OtherCostController@index');

    Route::get('otherincome/{id}', 'Api\OtherIncomeController@show');
    Route::post('otherincome', 'Api\OtherIncomeController@store');
    Route::put('otherincome/{id}', 'Api\OtherIncomeController@update');
    Route::delete('otherincome/{id}', 'Api\OtherIncomeController@destroy');
    Route::get('otherincome', 'Api\OtherIncomeController@index');

    Route::group(['middleware' => 'auth:api'], function(){
    
    Route::post('logout', 'Api\AuthController@logout');

});

