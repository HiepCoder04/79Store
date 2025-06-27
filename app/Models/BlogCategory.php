<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    // Chỉ định đúng tên bảng
    protected $table = 'category_blogs';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active'
    ];
    
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_blog_id');
    }
}