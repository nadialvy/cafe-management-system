<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    //Get all data
    public function show(){
        return Order::all();
    }

    //Create data
    public function store(Request $req){
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

        //check if table is available
        $table = Table::where('table_id', $req->table_id)->first();
        if($table->is_available == 'false'){
            return response()->json([
                'status' => 'failed',
                'message' => 'Table is not available, please select another table'
            ], 400);
        }

        $store = Order::create([
            'order_date' => $req->order_date,
            'user_id' => $req->user_id,
            'table_id' => $req->table_id,
            'customer_name' => $req->customer_name,
            'status' => $req->status,
        ]);

        $data = Order::where('order_date', $store->order_date)->first();
        if($store && $table->is_available == true){
            return response()->json([
                'status' => 'success',
                'message' => 'Data has been created',
                'data' => $data
            ], 200);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Data failed to create'
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
