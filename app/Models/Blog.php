<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_blog_id',
        'is_active',
    ];

    // Sửa lại quan hệ: một Blog thuộc về một BlogCategory
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_blog_id');
    }
}