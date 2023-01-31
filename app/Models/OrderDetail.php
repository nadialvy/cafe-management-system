<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'order_detail';
    public $timestamps = true;
    protected $primaryKey = 'order_detail_id';

    protected $fillable =[
        'order_id',
        'menu_id',
        'quantity',
        'price',
    ];
}
