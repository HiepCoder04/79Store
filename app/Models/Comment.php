<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'product_id',
        'name',
        'email',
        'content',
      
        'parent_id',
        'is_admin',
        'is_hidden',
    ];

    /**
     * Người gửi bình luận (có thể null nếu là khách).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sản phẩm được bình luận.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
