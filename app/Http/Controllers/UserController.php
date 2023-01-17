<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show(){
        return User::all();
    }

    public function store(Request $req){
        $validator = Validator::make($req->all(), [
            'user_name' => 'required|string|max:100',
            'role' => 'required|in:admin,cashier,manager',
            'username' => 'required|unique:user',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $store = User::create([
            'user_name' => $req->user_name,
            'role' => $req->role,
            'username' => $req->username,
            'password' => Hash::make($req->password),
        ]);

        $data = User::where('username', $store->username)->first();
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

    public function update($id, Request $req){
        $validator = Validator::make($req->all(), [
            'user_name' => 'required|string|max:100',
            'role' => 'required|in:admin,cashier,manager',
            'username' => 'required|unique:user',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $update = User::where('user_id', $id)->update([
            'user_name' => $req->user_name,
            'role' => $req->role,
            'username' => $req->username,
            'password' => Hash::make($req->password),
        ]);

        $data = User::where('user_id', $id)->first();
        if($update){
            return response()->json([
                'status' => 'success',
                'message' => 'Data has been updated',
                'data' => $data,
            ], 200);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Data failed to update',
            ], 400);
        }
    }

    public function delete($id){
        $delete = User::where('user_id', $id)->delete();
        if($delete){
            return response()->json([
                'status' => 'success',
                'message' => 'Data has been deleted'
            ], 200);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to delete data'
            ], 400);
        }
    }

}
