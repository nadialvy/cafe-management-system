<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    //Get all data
    public function show(){
        return Table::all();
    }

    //Get data available
    public function showAvailable(){
        return Table::where('is_available', true)->get();
    }

    //Create data
    public function store(Request $req){
        $validator = Validator::make($req->all(), [
            'table_number' => 'required|integer',
            'is_available' => 'required|in:true,false',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $store = Table::create([
            'table_number' => $req->table_number,
            'is_available' => $req->is_available,
        ]);

        $data = Table::where('table_number', $store->table_number)->first();
        if($store){
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
        $validator = Validator::make($req->all(), [
            'table_number' => 'required|integer',
            'is_available' => 'required|in:true,false',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $update = Table::where('table_id', $id)->update([
            'table_number' => $req->table_number,
            'is_available' => $req->is_available,
        ]);

        $data = Table::where('table_id', $id)->first();
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
        $delete = Table::where('table_id', $id)->delete();

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
