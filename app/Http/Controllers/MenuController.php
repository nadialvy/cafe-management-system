<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\File;

class MenuController extends Controller
{
    // Get all data
    public function show()
    {
        $data = DB::table('menu as m')
            ->select('m.*', 'mi.*')
            ->join('menu_image as mi', 'm.menu_id', '=', 'mi.menu_id')
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

    // Get data by id
    public function detail($id)
    {
        $data = DB::table('menu as m')
            ->select('m.*', 'mi.*')
            ->join('menu_image as mi', 'm.menu_id', '=', 'mi.menu_id')
            ->where('m.menu_id', $id)
            ->first();

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

    public function showFood()
    {
        $data = DB::table('menu as m')
            ->select('m.*', 'mi.*')
            ->join('menu_image as mi', 'm.menu_id', '=', 'mi.menu_id')
            ->where('m.type', 'food')
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

    public function showDrink()
    {
        $data = DB::table('menu as m')
            ->select('m.*', 'mi.*')
            ->join('menu_image as mi', 'm.menu_id', '=', 'mi.menu_id')
            ->where('m.type', 'drink')
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

    // Insert data
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'menu_name' => 'required|string|max:100',
            'type' => 'required|in:food,drink',
            'menu_description' => 'required|string',
            'price' => 'required|integer',
            'menu_image_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $validator->valid();

        // insert to menu table
        $menu = Menu::create($data);

        // insert to menu_image table
        $file = $req->file('menu_image_name');
        $newName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images'), $newName);

        $menuImage = new MenuImage();
        $menuImage->menu_id = $menu->id;
        $menuImage->menu_image_name = $newName;
        $menuImage->save();

        $data = Menu::where('menu_name', $menu->menu_name)->first();
        $dataImg = MenuImage::where('menu_image_name', $menuImage->menu_image_name)->first();
        if ($data && $dataImg) {
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

    // Update data
    public function update($id, Request $req)
    {
        $validator = Validator::make($req->all(), [
            'menu_name' => 'required|string|max:100',
            'type' => 'required|in:food,drink',
            'menu_description' => 'required|string',
            'price' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $update = Menu::where('menu_id', $id)->update([
            'menu_name' => $req->menu_name,
            'type' => $req->type,
            'menu_description' => $req->menu_description,
            'price' => $req->price
        ]);

        $data = Menu::where('menu_id', $id)->first();
        if ($update) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data has been updated',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Data failed to update'
            ], 400);
        }
    }

    // Delete data
    public function delete($id)
    {
        // delete the image first
        $data = MenuImage::where('menu_id', $id)->first();
        $image_path = public_path('images/' . $data->menu_image_name);
        if (file_exists(
            $image_path
        )) {
            unlink($image_path);
        }

        // delete the data
        $delete = Menu::where('menu_id', $id)->delete();
        if ($delete) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data has been deleted'
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Data failed to delete'
            ], 400);
        }
    }

    // Search query
    public function search($searchKey) //search all type
    {
        $data = DB::table('menu as m')
            ->select('m.*', 'mi.*')
            ->where('m.menu_name', 'like', "%$searchKey%")
            ->orWhere('m.type', 'like', "%$searchKey%")
            ->orWhere('m.price', 'like', "%$searchKey%")
            ->join('menu_image as mi', 'm.menu_id', '=', 'mi.menu_id')
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

    public function searchFood($searchKey){
        $allData = DB::table('menu as m')
            ->select('m.*', 'mi.*')
            ->where('m.menu_name', 'like', "%$searchKey%")
            ->orWhere('m.type', 'like', "%$searchKey%")
            ->orWhere('m.price', 'like', "%$searchKey%")
            ->join('menu_image as mi', 'm.menu_id', '=', 'mi.menu_id')
            ->get();

        $food = $allData->where('type', 'food');

        if ($food && $food->count() > 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'Get data success',
                'data' => $food
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Data not found'
            ], 404);
        }
    }

    public function searchDrink($searchKey){
        $allData = DB::table('menu as m')
            ->select('m.*', 'mi.*')
            ->where('m.menu_name', 'like', "%$searchKey%")
            ->orWhere('m.type', 'like', "%$searchKey%")
            ->orWhere('m.price', 'like', "%$searchKey%")
            ->join('menu_image as mi', 'm.menu_id', '=', 'mi.menu_id')
            ->get();

        $drink = $allData->where('type', 'drink');

        if ($drink && $drink->count() > 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'Get data success',
                'data' => $drink
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Data not found'
            ], 404);
        }
    }
}
