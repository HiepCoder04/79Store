<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;
class Category extends Model
{
    use HasFactory,Filterable;

    // Cho phép mass assignment cho các trường này
    protected $fillable = ['name', 'parent_id'];

    /**
     * Quan hệ parent (danh mục cha)
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
