<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    // Get all data
    public function show(){
        return Menu::all();
    }

    // Get data by id
    public function detail($id){
        $data = Menu::where('menu_id', $id)->first();
        if($data){
            return response()->json([
                'status' => 'success',
                'message' => 'Data has been found',
                'data' => $data
            ], 200);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Data not found'
            ], 404);
        }
    }

    // Insert data
    public function store(Request $req){
        $validator = Validator::make($req->all(), [
            'menu_name' => 'required|string|max:100',
            'type' => 'required|in:food,drink',
            'menu_description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $store = Menu::create([
            'menu_name' => $req->menu_name,
            'type' => $req->type,
            'menu_description' => $req->menu_description,
            'image' => $req->image,
            'price' => $req->price,
        ]);

        $data = Menu::where('menu_name', $store->menu_name)->first();
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

    // Update data
    public function update($id, Request $req){
        $validator = Validator::make($req->all(), [
            'menu_name' => 'required|string|max:100',
            'type' => 'required|in:food,drink',
            'menu_description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $update = Menu::where('menu_id', $id)->update([
            'menu_name' => $req->menu_name,
            'type' => $req->type,
            'menu_description' => $req->menu_description,
            'image' => $req->image,
            'price' => $req->price,
        ]);

        $data = Menu::where('menu_id', $id)->first();
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

    // Delete data
    public function delete($id){
        $delete = Menu::where('menu_id', $id)->delete();
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
