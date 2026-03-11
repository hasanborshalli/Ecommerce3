<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        view()->composer('*', function ($view) {

            // ── Site settings (cached 1 hr) ──────────────────────────
            $settings = Cache::remember('site_settings_all', 3600, function () {
                return SiteSetting::pluck('value', 'key')->toArray();
            });

            $siteName         = $settings['site_name']         ?? 'Luma';
            $currencySymbol   = $settings['currency_symbol']   ?? '$';
            $freeShippingOver = (float) ($settings['free_shipping_over'] ?? 150);
            $shippingCost     = (float) ($settings['shipping_cost']       ?? 9);

            $socialLinks = [
                'instagram' => $settings['social_instagram'] ?? '',
                'facebook'  => $settings['social_facebook']  ?? '',
                'twitter'   => $settings['social_twitter']   ?? '',
            ];

            // ── Active categories for nav ────────────────────────────
            $navCategories = Cache::remember('nav_categories', 3600, function () {
                return Category::active()
                    ->orderBy('sort_order')
                    ->select('id', 'name', 'slug', 'image')
                    ->get();
            });

            // ── Cart count ───────────────────────────────────────────
            $cartCount = 0;
            if (session()->has('cart')) {
                foreach (session('cart', []) as $item) {
                    $cartCount += (int) ($item['quantity'] ?? 1);
                }
            }

            // ── Unread messages (admin topbar) ───────────────────────
            $unreadMessages = Cache::remember('unread_messages_count', 300, function () {
                return ContactMessage::unread()->count();
            });

            $view->with(compact(
                'settings',
                'siteName',
                'currencySymbol',
                'freeShippingOver',
                'shippingCost',
                'socialLinks',
                'navCategories',
                'cartCount',
                'unreadMessages',
            ));
        });
    }
}