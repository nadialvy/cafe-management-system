<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderDetailController extends Controller
{
    //Get by id
    public function detail($id)
    {
        if (DB::table('order')->where('order_id', $id)->exists()) {
            $detail = DB::table('order_detail')
                ->select('order_detail.*', 'menu.*', 'order.*')
                ->where('order.order_id', $id)
                ->join('order', 'order.order_id', '=', 'order_detail.order_id')
                ->join('menu', 'menu.menu_id', '=', 'order_detail.menu_id')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Get data success',
                'data' => $detail
            ], 200);
        }else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Could not find the data',
            ], 401);
        }
    }
}
