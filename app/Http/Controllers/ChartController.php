<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChartController extends Controller{
  
  // get data order today
  public function getTodayData()
  {
    $totalOrderToday = DB::table('order')
      ->whereDate('order_date', Carbon::today())
      ->count();

    $revenueToday = DB::table('order')
      ->whereDate('order_date', Carbon::today())
      ->join('order_detail', 'order.order_id', '=', 'order_detail.order_id')
      ->sum(DB::raw('order_detail.quantity * order_detail.price'));

    $data = [
      'totalOrder' => $totalOrderToday,
      'revenue' => $revenueToday
    ];

    if ($data) {
      return response()->json([
        'status' => 'success',
        'message' => 'Get data success',
        'data' => $data
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'Data not found'
      ], 404);
    }
  }

  // get data order this week
  public function getThisWeekData()
  {
    $totalOrderThisWeek = DB::table('order')
      ->whereBetween('order_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
      ->count();
 
    $revenueThisWeek = DB::table('order')
      ->whereBetween('order_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
      ->join('order_detail', 'order.order_id', '=', 'order_detail.order_id')
      ->sum(DB::raw('order_detail.quantity * order_detail.price'));

    $data = [
      'totalOrder' => $totalOrderThisWeek,
      'revenue' => $revenueThisWeek
    ];

    if ($data) {
      return response()->json([
        'status' => 'success',
        'message' => 'Get data success',
        'data' => $data
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'Data not found'
      ], 404);
    }
  }
}