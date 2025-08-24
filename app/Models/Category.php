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

    /**
     * Quan hệ với các danh mục con
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Quan hệ với sản phẩm
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope để lấy chỉ danh mục gốc (không có parent)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope để lấy chỉ danh mục con (có parent)
     */
    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Kiểm tra xem danh mục này có phải là danh mục gốc không
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }

    /**
     * Kiểm tra xem danh mục này có danh mục con không
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * Lấy tất cả ID của danh mục con (đệ quy)
     */
    public function getAllChildrenIds()
    {
        $childrenIds = [];
        
        foreach ($this->children as $child) {
            $childrenIds[] = $child->id;
            $childrenIds = array_merge($childrenIds, $child->getAllChildrenIds());
        }
        
        return $childrenIds;
    }

    /**
     * Lấy đường dẫn danh mục (breadcrumb)
     */
    public function getBreadcrumb($separator = ' > ')
    {
        $path = [];
        $category = $this;
        
        while ($category) {
            array_unshift($path, $category->name);
            $category = $category->parent;
        }
        
        return implode($separator, $path);
    }

    /**
     * Scope search cho trait Searchable (nếu có)
     */
    public function scopeSearch($query, $request, $fields = ['name'])
    {
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm, $fields) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'like', "%{$searchTerm}%");
                }
            });
        }
        
        return $query;
    }
}
