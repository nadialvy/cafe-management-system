<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class MenuImageController extends Controller
{
    public function update($menu_id, Request $req)
    {
        $validator = Validator::make($req->input(), [
            'menu_image_name' => [
                'required',
                File::types(['jpeg', 'jpg', 'png', 'gif', 'svg'])
                    ->max(1024 * 1024 * 5, '5MB')
            ],
        ]);

        dd($req->menu_image_name === null);

        if (!$req->hasFile('menu_image_name')) {
            return response()->json(['error' => 'menu_image_name is required'], 400);
        }

        $validator = Validator::make($req->all(), [
            'menu_image_name' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $menu = Menu::where('menu_id', $menu_id)->first();
        if ($menu) {
            // delete old image
            $menu_image = MenuImage::where('menu_id', $menu_id)->firstOrFail();
            $image_path = public_path('images/' . $menu_image->menu_image_name);
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            // update new image
            $file = $req->file('menu_image_name');
            $newName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $newName);

            $menuImage = MenuImage::where('menu_id', $menu_id)->update([
                'menu_image_name' => $newName
            ]);

            if ($menuImage) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Image has been updated',
                    'data' => $menuImage
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Image failed to update'
                ], 400);
            }
        }
    }
}
