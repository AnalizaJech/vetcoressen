<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        $locale = request()?->cookie('vc_locale') ?? request()?->query('lang') ?? session('locale') ?? 'en';
        if (!in_array($locale, ['es', 'en'])) {
            $locale = 'en';
        }
        app()->setLocale($locale);
        if ($locale === 'es') {
            \Carbon\Carbon::setLocale('es');
            setlocale(LC_TIME, 'es_ES.utf8', 'es_ES', 'esp');
        } else {
            \Carbon\Carbon::setLocale('en');
            setlocale(LC_TIME, 'en_US.utf8', 'en_US', 'eng');
        }
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.tailwind');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');
    }
}
