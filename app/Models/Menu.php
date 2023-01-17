<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menu';
    public $timestamps = true;

    protected $fillable = [
        'menu_name',
        'type',
        'menu_description',
        'image',
        'price',
    ];
}
