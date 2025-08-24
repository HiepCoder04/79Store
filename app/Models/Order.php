<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;

class Order extends Model
{
    use HasFactory, Filterable;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'name',
        'address_id',
        'phone',
        'note',
        'payment_method',
        'shipping_method',
        'total_before_discount',
        'discount_amount',
        'total_after_discount',
        'status',
        'sale_channel',
    ];

    // Nếu muốn các accessor tự có mặt khi ->toArray()/JSON
    protected $appends = [
        'order_code',
        'total_refunded_amount',
        'has_returns',
        'return_requests_count',
        'return_percentage',
        'return_status_text',
    ];

    /** Quan hệ: Order thuộc về User */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Quan hệ: Order có địa chỉ giao hàng */
    public function address()
    {
        return $this->belongsTo(UserAddress::class, 'address_id');
    }

    /** Quan hệ: Order có nhiều chi tiết đơn hàng */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function cancellations()
    {
        return $this->hasMany(Cancellation::class);
    }

    /** Yêu cầu hủy mới nhất */
    public function latestCancellation()
    {
        return $this->hasOne(Cancellation::class)->latestOfMany();
    }

    /** Quan hệ: Các yêu cầu trả hàng */
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    /** Mã đơn: 79ST + Ymd + id pad 4 */
    public function getOrderCodeAttribute()
    {
        $date = $this->created_at ? $this->created_at->format('Ymd') : now()->format('Ymd');
        $id = $this->id ?? 0;
        return '79ST' . $date . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    /** Tổng tiền đã hoàn (từ các yêu cầu trạng thái refunded) */
    public function getTotalRefundedAmountAttribute()
    {
        return $this->returnRequests()
            ->whereIn('status', ['refunded'])
            ->get()
            ->sum(function ($request) {
                if ($request->orderDetail) {
                    $productPrice = $request->orderDetail->product_price ?? 0;
                    $potPrice     = $request->orderDetail->pot_price ?? 0;
                    return ($productPrice * ((int) ($request->plant_quantity ?? 0)))
                         + ($potPrice     * ((int) ($request->pot_quantity   ?? 0)));
                }
                return 0;
            });
    }

    /** Có trả hàng không */
    public function getHasReturnsAttribute()
    {
        return $this->returnRequests()->exists();
    }

    /** Số yêu cầu trả hàng */
    public function getReturnRequestsCountAttribute()
    {
        return $this->returnRequests()->count();
    }

    /** % hoàn tiền so với tổng đơn */
    public function getReturnPercentageAttribute()
    {
        $total = (float) ($this->total_after_discount ?? 0);
        if ($total <= 0) {
            return 0;
        }
        $pct = ($this->total_refunded_amount / $total) * 100;
        return min(100, round($pct, 1));
    }

    /** Text trạng thái trả hàng gộp */
    public function getReturnStatusTextAttribute()
    {
        $pendingCount  = $this->returnRequests()->where('status', 'pending')->count();
        $refundedCount = $this->returnRequests()->where('status', 'refunded')->count();
        $rejectedCount = $this->returnRequests()->where('status', 'rejected')->count();

        if ($pendingCount > 0) {
            return "Có {$pendingCount} yêu cầu chờ duyệt";
        }
        if ($refundedCount > 0 && $rejectedCount > 0) {
            return "Đã hoàn {$refundedCount} yêu cầu, từ chối {$rejectedCount}";
        }
        if ($refundedCount > 0) {
            return "Đã hoàn {$refundedCount} yêu cầu";
        }
        if ($rejectedCount > 0) {
            return "Đã từ chối {$rejectedCount} yêu cầu";
        }
        return $this->has_returns ? "Có yêu cầu trả hàng" : "Không có yêu cầu trả hàng";
    }

    /** ✅ THÊM METHOD MỚI: Tính tổng số lượng sản phẩm trong đơn */
    public function getTotalItemsQuantityAttribute()
    {
        return $this->orderDetails->sum('quantity');
    }

    /** ✅ THÊM METHOD MỚI: Tính tổng số lượng đã trả (cả cây và chậu) */
    public function getTotalReturnedQuantityAttribute()
    {
        return $this->returnRequests()
            ->whereIn('status', ['refunded', 'exchanged'])
            ->get()
            ->sum(function ($request) {
                return max($request->plant_quantity ?? 0, $request->pot_quantity ?? 0);
            });
    }

    /** ✅ THÊM METHOD MỚI: Text hiển thị trạng thái trả hàng ngắn gọn */
    public function getReturnBadgeTextAttribute()
    {
        if (!$this->has_returns || $this->status !== 'delivered') {
            return null;
        }

        $returnedQty = $this->total_returned_quantity;
        $totalQty = $this->total_items_quantity;

        if ($returnedQty <= 0) {
            return null;
        }

        if ($returnedQty >= $totalQty) {
            return "Đã hoàn trả hết";
        }

        return "Có {$returnedQty} sản phẩm đã trả";
    }
}
