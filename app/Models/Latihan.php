<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Latihan extends Model
{
    use HasFactory;
    protected $table = 'latihan';
    protected $fillable = [
        'isCheckin',
        'dateTime',
        'user_id',
    ];
    protected $hidden = [
        'updated_at',
        'created_at',
    ];
}
