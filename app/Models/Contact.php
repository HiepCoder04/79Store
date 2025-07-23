<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // Khai báo các trường được phép gán
    protected $fillable = ['name', 'email', 'message'];
}
