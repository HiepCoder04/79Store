<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [/* ... */];

    public function boot(): void
    {
        // Quyền sở hữu đơn: chỉ chủ đơn mới thao tác
        Gate::define('own-order', function ($user, Order $order) {
            return (int) $order->user_id === (int) $user->id;
        });
    }
}
