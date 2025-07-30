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
            // Lấy đối tượng Cart với count của các items liên quan
            $cart = Cart::where('user_id', auth()->id())
                ->withCount('items')
                ->first();
            $count = $cart ? $cart->items_count : 0;
        }
        $view->with('cartItemCount', $count);
    });
    }
}
