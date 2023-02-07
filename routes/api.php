<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\MenuImageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartController;

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

Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['jwt.verify:cashier,manager,admin']],  function () {
  Route::post('refresh', [AuthController::class, 'refresh']);
  Route::get('user-profile', [AuthController::class, 'userProfile']);
  Route::post('logout', [AuthController::class, 'logout']);
  Route::put('updatepass/{id}', [UserController::class, 'updatePassword']);
  
  Route::group(['middleware' => ['jwt.verify:cashier']], function () {
    Route::post('order', [OrderController::class, 'store']); // 2 dan 3. melakukan transaksi + menentukan no meja yang tersedia
    Route::get('order/{id}', [OrderController::class, 'showByUser']); // 4. melihat transaksi nya sendiri
    Route::put('order/{id}/status', [OrderController::class, 'updateStatus']); // 5. mengubah status pemnayaran
    Route::get('orderdetail/{id}', [OrderDetailController::class, 'detail']); // 6. invoice
    Route::put('order/{id}', [OrderController::class, 'update']); // 7. melakukan update jika pelanggan memesan lagi
  });

  Route::group(['middleware' => ['jwt.verify:manager']], function () {
    Route::get('order', [OrderController::class, 'show']); // 2.melihat seluruh daftar transaksi
    Route::get('order/search/{searchKey}', [OrderController::class, 'search']); // 3.search berdasarkan apapun
    Route::get('order/searchbydate/{date}', [OrderController::class, 'searchByDate']); //  4. search tanggal
    Route::get('order/searchbymonth/{date}', [OrderController::class, 'searchByMonth']); // 5. search bulan
    Route::get('menu/show/bestseller', [MenuController::class, 'bestSeller']); // 6. pie chart best seller

    // addition
    Route::get('todaydata', [ChartController::class, 'getTodayData']);
    Route::get('weekdata', [ChartController::class, 'getThisWeekData']);
  });

  Route::group(['middleware' => ['jwt.verify:admin']], function () {
    // 2. CRUD user
    Route::get('user', [UserController::class, 'show']);
    Route::get('user/search/{searchKey}', [UserController::class, 'search']);
    Route::post('user', [UserController::class, 'store']);
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'delete']);
    Route::put('user/forgotpass/{id}', [UserController::class, 'forgotPassword']);

    // 3. CRUD menu
    Route::post('menu', [MenuController::class, 'store']);
    Route::put('menu/{id}', [MenuController::class, 'update']);
    Route::delete('menu/{id}', [MenuController::class, 'delete']);
    Route::post('menuimage/{id}', [MenuImageController::class, 'update']);

    // 4. CRUD meja
    Route::post('table', [TableController::class, 'store']);
    Route::put('table/{id}', [TableController::class, 'update']); //id = table_number
    Route::delete('table/{id}', [TableController::class, 'delete']); //id = table_number
  });

  Route::get('user/cashier', [UserController::class, 'showCashier']);

  Route::get('menu', [MenuController::class, 'show']);
  Route::get('menu/search/{searchKey}', [MenuController::class, 'search']);
  Route::get('menu/searchfood/{searchKey}', [MenuController::class, 'searchFood']);
  Route::get('menu/searchdrink/{searchKey}', [MenuController::class, 'searchDrink']);
  Route::get('menu/{id}', [MenuController::class, 'detail']);
  Route::get('menu/show/food', [MenuController::class, 'showFood']);
  Route::get('menu/show/drink', [MenuController::class, 'showDrink']);
  Route::get('detailmenu/{id}', [OrderDetailController::class, 'detailMenu']);

  Route::get('table', [TableController::class, 'show']);
  Route::get('table/available', [TableController::class, 'showAvailable']);
  Route::get('table/availableedit/{id}', [TableController::class, 'showAvailableForEdit']); //id = table_id
  Route::get('table/search/{searchKey}', [TableController::class, 'search']);

  // idk if this necessary or not but i'll leave it here
  Route::delete('order/{id}', [OrderController::class, 'delete']);
});
