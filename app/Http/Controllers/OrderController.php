<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Table;
use App\Models\Menu;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //Get all data
    public function show(){
        $data = DB::table('order as o')
            ->select('o.*', 't.*', 'u.*')
            ->join('table as t', 'o.table_id', '=', 't.table_id')
            ->join('user as u', 'o.user_id', '=', 'u.user_id')
            ->orderBy('o.order_id', 'asc')
            ->get();

        if($data){
            return response()->json([
                'status' => 'success',
                'message' => 'Get data success',
                'data' => $data
            ], 200);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Data not found'
            ], 404);
        }
    }

    //Create data
    public function store(Request $req){
        $validator = Validator::make($req->all(),[
            'user_id' => 'required|integer',
            'table_id' => 'required|integer',
            'customer_name' => 'required|string|max:100',
            'detail' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        //check if table status is available
        $table = Table::where('table_id', $req->table_id)->first();
        if($table->is_available == 'false'){
            return response()->json([
                'status' => 'failed',
                'message' => 'Table is not available, please select another table'
            ], 400);
        }

        // insert order
        $order = new Order();
        $order->order_date = Carbon::now();
        $order->user_id = $req->user_id;
        $order->table_id = $req->table_id;
        $order->customer_name = $req->customer_name;
        $order->status = 'pending';
        $order->save();

        // insert detail
        for($i = 0; $i < count($req->detail); $i++){
            $detail = new OrderDetail();
            $detail->order_id = $order->id;
            $detail->menu_id = $req->detail[$i]['menu_id'];
            $detail->quantity = $req->detail[$i]['quantity'];

            // get price from menu
            $price = Menu::where('menu_id', $req->detail[$i]['menu_id'])->first()->price;
            $detail->price = $detail->quantity * $price ;
            $detail->save();
        }

        // change table status
        $table->table_id = Table::where('table_id', $order->table_id)->update([
            'is_available' => 'false'
        ]);

        $dataOrder = Order::where('order_id', $order->id)->first();
        $dataDetail = OrderDetail::where('order_id', $order->id)->get();

        if($order && $detail){
            return response()->json([
                'status' => 'success',
                'message' => 'Data has been created',
                'data' => [
                    'order' => $dataOrder,
                    'detail' => $dataDetail
                ]
            ], 200);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Data failed to create'
            ], 400);
        }

    }

    // update status only
    public function updateStatus($id, Request $req){
        $validator = Validator::make($req->all(),[
            'status' => 'required|in:paid,pending',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $update = Order::where('order_id', $id)->update([
            'status' => $req->status,
        ]);

        // get data based on id in order to get table_id
        $data = Order::where('order_id', $id)->first();

        // if status is paid, change table status to available
        if($req->status == 'paid'){
            Table::where('table_id', $data->table_id)->update([
                'is_available' => 'true'
            ]);
        }

        if($update){
            return response()->json([
                'status' => 'success',
                'message' => 'Data has been updated',
                'data' => $update
            ], 200);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Data failed to update'
            ], 400);
        }
    }

    //Update data
    public function update($id, Request $req){
        $validator = Validator::make($req->all(),[
            'order_date' => 'required|date',
            'user_id' => 'required|integer',
            'table_id' => 'required|integer',
            'customer_name' => 'required|string|max:100',
            'status' => 'required|in:paid,pending',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $update = Order::where('order_id', $id)->update([
            'order_date' => $req->order_date,
            'user_id' => $req->user_id,
            'table_id' => $req->table_id,
            'customer_name' => $req->customer_name,
            'status' => $req->status,
        ]);

        // if status is paid, change table status to available
        if($req->status == 'paid'){
            Table::where('table_id', $req->table_id)->update([
                'is_available' => 'true'
            ]);
        }

        $data = Order::where('order_id', $id)->first();
        if($update){
            return response()->json([
                'status' => 'success',
                'message' => 'Data has been updated',
                'data' => $data
            ], 200);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Data failed to update'
            ], 400);
        }

    }

    //Delete data
    public function delete($id){
        $delete = Order::where('id', $id)->delete();
        if($delete){
            return response()->json([
                'status' => 'success',
                'message' => 'Data has been deleted'
            ], 200);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Data failed to delete'
            ], 400);
        }
    }

}
