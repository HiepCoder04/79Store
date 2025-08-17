<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class searchController extends Controller
{
    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $products = Product::query()
            ->with(['galleries' => function ($qr) {
                // đổi 'image' bên dưới nếu cột ảnh của bảng galleries tên khác (vd: image_path/url)
                $qr->select('id', 'product_id', 'image')->orderBy('id');
            }])
            ->where('name', 'like', "%{$q}%")
            ->select('id', 'name', 'slug')   // <<<<<<<<<<  BỎ 'image' ở đây
            ->limit(8)
            ->get();

        $items = $products->map(function ($p) {
            $first = $p->galleries->first();

            // Lấy tên cột ảnh đúng của bạn: ưu tiên $first->image, nếu không có thử image_path/url
            $raw = $first->image
                ?? ($first->image_path ?? null)
                ?? ($first->url ?? null);

            // Chuẩn hoá URL ảnh
            if ($raw) {
                if (Str::startsWith($raw, ['http://', 'https://'])) {
                    $thumb = $raw;
                } else {
                    // nếu ảnh lưu trong storage/app/public/...
                    $thumb = Storage::exists($raw) ? Storage::url($raw) : asset($raw);
                }
            } else {
                $thumb = asset('images/no-image.png');
            }

            return [
                'id'    => $p->id,
                'name'  => $p->name,
                'slug'  => $p->slug,   // FE sẽ build link /san-pham/{slug}
                'thumb' => $thumb,
            ];
        })->values();

        return response()->json($items);
    }
}
