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
        'category_blog_id', // Đổi từ category_id sang category_blog_id
        'is_active',
        'img',
    ];

    // Sửa lại quan hệ để sử dụng đúng tên cột
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_blog_id');
    }
}