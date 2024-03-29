<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $table = 'table';
    public $timestamps = true;

    protected $fillable = [
        'table_number',
        'is_available',
    ];
}
