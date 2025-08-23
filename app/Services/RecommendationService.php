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
            // Lấy tất cả order_id chứa product này
            $orderIds = OrderDetail::where('product_id', $productId)->pluck('order_id');
            if ($orderIds->isEmpty()) {
                return collect();
            }

            // Lấy các product_id khác trong những order đó
            $rows = OrderDetail::whereIn('order_id', $orderIds)
                ->where('product_id', '!=', $productId)
                ->selectRaw('product_id, COUNT(*) as cnt')
                ->groupBy('product_id')
                ->orderByDesc('cnt')
                ->limit($limit)
                ->get();

            $ids = $rows->pluck('product_id')->all(); // ✅ chuyển thành array

            return Product::with(['galleries' => fn($q) => $q->limit(1), 'variants'])
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn($p) => array_search($p->id, $ids)) // giữ nguyên thứ tự
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
