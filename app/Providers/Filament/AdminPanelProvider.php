<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->unsavedChangesAlerts()
            ->databaseTransactions()
//            ->databaseNotifications()
//            ->strictAuthorization()
//            ->profile(EditProfile::class, isSimple: false)
            ->login()
//            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->emailChangeVerification()
//            ->brandLogo(asset('images/xxx.png'))
//            ->darkModeBrandLogo(asset('images/xxx-dark.png'))
//            ->viteTheme('resources/css/filament/admin/theme.css')
            ->sidebarWidth('16rem')
            ->colors([
                'primary' => Color::Amber,
                //                'primary' => Color::convertToOklch('#4397cb'),
                //                'gray' => Color::Slate,
                //                'info' => Color::Violet,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                // EnsureEmailIsVerified::class, // todo: check ==== ->emailVerification() ?
                // CheckAdminAccess::class, // ToDO: create can Acces Panel permission middleware?? => or put in User model?
            ]);
    }
}
