<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
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
        Paginator::useBootstrapFive();
        View::composer('*', function ($view) {
            $notifications = auth()->check() ? auth()->user()->notifications()->latest()->take(10)->get() : collect();
            $view->with('notifications', $notifications);
        });
         
    View::composer('*', function ($view) {
        $cart_count = 0;

        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            $cart_count = $cart->items()->sum('quantity');
        }

        $view->with('cart_count', $cart_count);
    });
    View::composer('*', function ($view) {
        $favoritesCount = 0;
        if (Auth::check()) {
            $favoritesCount = Auth::user()->favorites()->count();
        }
        $view->with('favoritesCount', $favoritesCount);
    });
    }
    
}
