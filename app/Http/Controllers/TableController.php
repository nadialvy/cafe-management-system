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
            'table_number' => 'required|integer|unique:table',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $store = Table::create([
            'table_number' => $req->table_number,
            'is_available' => 'true',
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
    public function update($tableNumber, Request $req){
        $validator = Validator::make($req->all(), [
            'table_number' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $update = Table::where('table_number', $tableNumber)->update([
            'table_number' => $req->table_number,
        ]);

        $data = Table::where('table_number', $tableNumber)->first();
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
    public function delete($table_number){
        $delete = Table::where('table_number', $table_number)->delete();

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
