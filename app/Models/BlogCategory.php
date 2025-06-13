<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;

    // Tên bảng là 'category_blogs' theo ghi chú trong controller của bạn
    protected $table = 'category_blogs';

    protected $fillable = [
        'name'
    ];

    /**
     * Lấy danh sách blog thuộc danh mục này
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_blog_id');
    }
}