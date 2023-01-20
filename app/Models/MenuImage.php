<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuImage extends Model
{
    use HasFactory;
    protected $table = 'menu_image';
    public $timestamps = true;

    protected $fillable = [
        'menu_image_name',
    ];
}
