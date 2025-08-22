<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Cart;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $count = 0;

            if (auth()->check()) {
                $cart = Cart::where('user_id', auth()->id())->first();

                // ✅ tính tổng quantity thay vì chỉ đếm số item
                if ($cart) {
                    $count = $cart->items()->sum('quantity');
                }
            }

            $view->with('cartItemCount', $count);
        });
    }
}
