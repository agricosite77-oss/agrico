<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        if (! app()->runningInConsole() && request()->hasHeader('Host')) {
            $isHttps = request()->isSecure()
                || strtolower((string) request()->header('x-forwarded-proto')) === 'https'
                || str_contains(request()->getHost(), 'loca.lt')
                || str_contains(request()->getHost(), 'ngrok');

            if ($isHttps) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }

            \Illuminate\Support\Facades\URL::forceRootUrl(($isHttps ? 'https://' : request()->getScheme() . '://') . request()->getHttpHost());
        }

        // Register view locations so partials and pages in admin, website, mobile resolve seamlessly
        View::addLocation(resource_path('views/website'));
        View::addLocation(resource_path('views/admin'));
        View::addLocation(resource_path('views/mobile'));

        // Register View Hint Namespaces for <x-layouts::*> and <x-pages::*> tags
        View::addNamespace('layouts', [
            resource_path('views/admin/layouts'),
            resource_path('views/website/layouts'),
            resource_path('views/mobile/layouts'),
        ]);

        View::addNamespace('pages', [
            resource_path('views/website/pages'),
            resource_path('views/admin/pages'),
        ]);

        Blade::anonymousComponentPath(resource_path('views/admin/components'), 'admin');
        Blade::anonymousComponentPath(resource_path('views/website/components'), 'website');
        Blade::anonymousComponentPath(resource_path('views/mobile/components'), 'mobile');
        Blade::anonymousComponentPath(resource_path('views/admin/components'), 'dashboard');
        Blade::anonymousComponentPath(resource_path('views/admin/livewire/dashboard'), 'dashboard');

        Blade::anonymousComponentPath(resource_path('views/admin/layouts'), 'admin-layouts');
        Blade::anonymousComponentPath(resource_path('views/website/layouts'), 'website-layouts');
        Blade::anonymousComponentPath(resource_path('views/mobile/layouts'), 'mobile-layouts');

        // Fallbacks for global component tags
        Blade::anonymousComponentPath(resource_path('views/website/components'), '');
        Blade::anonymousComponentPath(resource_path('views/admin/components'), '');
        Blade::anonymousComponentPath(resource_path('views/mobile/components'), '');
        Blade::anonymousComponentPath(resource_path('views/admin/layouts'), 'layouts');
        Blade::anonymousComponentPath(resource_path('views/website/layouts'), 'layouts');

        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
