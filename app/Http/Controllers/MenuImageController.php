<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MenuImageController extends Controller
{
    public function update($menu_id, Request $request)
    {
        // delete old image
        $menuImage = MenuImage::where('menu_id', $menu_id)->first();
        $oldImagePath = $menuImage->menu_image_name;
        Storage::delete($oldImagePath);

        $file = $request->file('menu_image');
        $newName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images'), $newName);

        $updated = DB::table('menu_image')
            ->where('menu_id', $menu_id)
            ->update([
                'menu_image_name' => $newName
        ]);

        if($updated) {
            return response()->json([
                'status' => 'success',
                'message' => 'Update image success',
                'data' => $menuImage
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Update image failed'
            ], 404);
        }
    }
}
