<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\MenuImageController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('user', [UserController::class, 'show']);
Route::get('user/cashier', [UserController::class, 'showCashier']);
Route::post('user', [UserController::class, 'store']);
Route::put('user/{id}', [UserController::class, 'update']);
Route::delete('user/{id}', [UserController::class, 'delete']);

Route::get('menu', [MenuController::class, 'show']);
Route::get('menu/{id}', [MenuController::class, 'detail']);
Route::get('menu/show/food', [MenuController::class, 'showFood']);
Route::get('menu/show/drink', [MenuController::class, 'showDrink']);
Route::post('menu', [MenuController::class, 'store']);
Route::put('menu/{id}', [MenuController::class, 'update']);
Route::delete('menu/{id}', [MenuController::class, 'delete']);
Route::get('menu/search/{searchKey}', [MenuController::class, 'search']);
Route::get('menu/searchfood/{searchKey}', [MenuController::class, 'searchFood']);
Route::get('menu/searchdrink/{searchKey}', [MenuController::class, 'searchDrink']);

Route::get('table', [TableController::class, 'show']);
Route::get('table/available', [TableController::class, 'showAvailable']);
Route::post('table', [TableController::class, 'store']);
Route::put('table/{id}', [TableController::class, 'update']); //id = table_number
Route::delete('table/{id}', [TableController::class, 'delete']); //id = table_number
Route::get('table/search/{searchKey}', [TableController::class, 'search']);

Route::get('order', [OrderController::class, 'show']);
Route::post('order', [OrderController::class, 'store']);
Route::put('order/{id}', [OrderController::class, 'update']);
Route::put('order/{id}/status', [OrderController::class, 'updateStatus']);
Route::delete('order/{id}', [OrderController::class, 'delete']);

Route::get('orderdetail/{id}', [OrderDetailController::class, 'detail']);

Route::put('menuimage/{id}', [MenuImageController::class, 'update']);
