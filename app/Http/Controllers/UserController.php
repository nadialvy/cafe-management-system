<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
  public function show()
  {
    return User::all();
  }

  // get data cashier only
  public function showCashier()
  {
    $data = User::where('role', 'cashier')->get();
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

  public function store(Request $req)
  {
    $validator = Validator::make($req->all(), [
      'user_name' => 'required|string|max:100',
      'role' => 'required|in:admin,cashier,manager',
      'username' => 'required|unique:user',
      // 'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }

    $store = User::create([
      'user_name' => $req->user_name,
      'role' => $req->role,
      'username' => $req->username,
      // set default password and hash
      'password' => Hash::make('123456'),
    ]);

    $data = User::where('username', $store->username)->first();
    if ($store) {
      return response()->json([
        'status' => 'success',
        'message' => 'Data has been created',
        'data' => $data
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'Data failed to create'
      ], 400);
    }
  }

  public function update($id, Request $req)
  {
    $validator = Validator::make($req->all(), [
      'user_name' => 'required|string|max:100',
      'role' => 'required|in:admin,cashier,manager',
      'username' => 'required|unique:user,username,' . $id . ',user_id', //check unique username except id
      // 'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }

    $update = User::where('user_id', $id)->update([
      'user_name' => $req->user_name,
      'role' => $req->role,
      'username' => $req->username,
      // 'password' => Hash::make($req->password),
    ]);

    $data = User::where('user_id', $id)->first();
    if ($update) {
      return response()->json([
        'status' => 'success',
        'message' => 'Data has been updated',
        'data' => $data,
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'Data failed to update',
      ], 400);
    }
  }

  public function updatePassword(Request $request, $id)
  {
    Validator::make($request->all(), [
      'old_password' => 'required|string|min:6',
      'new_password' => 'required|string|min:6',
      'confirm_password' => 'required|string|min:6',
    ]);

    // check old password
    $user = User::where('user_id', $id)->first();
    if (!Hash::check($request->old_password, $user->password)) {
      return response()->json([
        'status' => 'failed',
        'message' => 'Old password is wrong'
      ], 400);
    }

    // check new password and confirm password
    if ($request->new_password != $request->confirm_password) {
      return response()->json([
        'status' => 'failed',
        'message' => 'New password and confirm password is not match'
      ], 400);
    }

    $update = User::where('user_id', $id)->update([
      'password' => Hash::make($request->new_password),
    ]);

    $data = User::where('user_id', $id)->first();
    if ($update) {
      return response()->json([
        'status' => 'success',
        'message' => 'Password has been changed',
        'data' => $data,
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'Failed to change password',
      ], 400);
    }
  }

  public function forgotPassword($id){
    $update = User::where('user_id', $id)->update([
      'password' => Hash::make('123456'),
    ]);

    $data = User::where('user_id', $id)->first();
    if ($update) {
      return response()->json([
        'status' => 'success',
        'message' => 'Success to reset password',
        'data' => $data,
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'Failed to reset password',
      ], 400);
    }
  }

  public function delete($id)
  {
    $delete = User::where('user_id', $id)->delete();
    if ($delete) {
      return response()->json([
        'status' => 'success',
        'message' => 'Data has been deleted'
      ], 200);
    } else {
      return response()->json([
        'status' => 'failed',
        'message' => 'Failed to delete data'
      ], 400);
    }
  }

  public function search($searchKey)
  {
    $data = DB::table('user')
      ->select('user.*')
      ->where('user_name', 'like',  "%$searchKey%")
      ->orWhere('username', 'like',  "%$searchKey%")
      ->orWhere('role', 'like',  "%$searchKey%")
      ->get();

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
