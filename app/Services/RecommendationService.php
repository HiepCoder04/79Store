<?php

namespace App\Services;

use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    /**
     * Gợi ý theo "thường mua cùng".
     */
     public function getRecommendations(int $productId, int $limit = 6)
    {
        return Cache::remember("recs:pid:$productId:$limit", 3600, function () use ($productId, $limit) {
            // 1. Lấy order_ids chứa product này
            $orderIds = OrderDetail::where('product_id', $productId)->pluck('order_id');
            $ids = [];

            if ($orderIds->isNotEmpty()) {
                // 2. Lấy product_id khác trong các đơn đó
                $rows = OrderDetail::whereIn('order_id', $orderIds)
                    ->where('product_id', '!=', $productId)
                    ->selectRaw('product_id, COUNT(*) as cnt')
                    ->groupBy('product_id')
                    ->orderByDesc('cnt')
                    ->limit($limit)
                    ->get();

                $ids = $rows->pluck('product_id')->all();
            }

            // 3. Nếu chưa đủ sản phẩm → lấy thêm cùng danh mục
            if (count($ids) < $limit) {
                $categoryId = Product::where('id', $productId)->value('category_id');
                $extra = Product::where('category_id', $categoryId)
                    ->where('id', '!=', $productId)
                    ->inRandomOrder()
                    ->limit($limit - count($ids))
                    ->pluck('id')
                    ->all();
                $ids = array_merge($ids, $extra);
            }

            // 4. Nếu vẫn chưa đủ → fallback top bán chạy
            if (count($ids) < $limit) {
                $extra = OrderDetail::selectRaw('product_id, SUM(quantity) as qty')
                    ->groupBy('product_id')
                    ->orderByDesc('qty')
                    ->limit($limit - count($ids))
                    ->pluck('product_id')
                    ->all();
                $ids = array_merge($ids, $extra);
            }

            // 5. Lấy sản phẩm theo thứ tự id trong mảng
            return Product::with(['galleries', 'variants']) // ✅ lấy tất cả ảnh
    ->whereIn('id', $ids)
    ->get()
    ->sortBy(fn($p) => array_search($p->id, $ids)) // giữ đúng thứ tự
    ->values();
        });
    }

    /**
     * Fallback: top bán chạy.
     */
    public function bestSellers(int $limit = 6)
    {
        $ids = OrderDetail::selectRaw('product_id, SUM(quantity) as qty')
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit($limit)
            ->pluck('product_id')
            ->all(); // ✅ thêm ->all() để thành array

        return Product::with(['galleries' => fn($q) => $q->limit(1), 'variants'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn($p) => array_search($p->id, $ids)) // giữ thứ tự đúng
            ->values();
    }
}
