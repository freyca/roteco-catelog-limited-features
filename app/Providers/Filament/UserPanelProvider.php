<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\User\Pages\Auth\EditProfile;
use App\Http\Middleware\PushPurchasedItemsToCart;
use App\Http\Middleware\RedirectsAdminUsersToAdminPanel;
use Filament\Auth\Pages\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('user')
            ->path('user')
            ->passwordReset()
            ->login(Login::class)
            // ->registration(Register::class)
            ->profile(
                page: EditProfile::class,
                isSimple: false
            )
            ->darkMode(false)
            ->topbar()
            ->brandLogo(config('custom.web_logo'))
            ->navigationItems([
                NavigationItem::make(__('Home'))
                    ->url('/')
                    ->icon('heroicon-o-home')
                    ->group(__('Website urls'))
                    ->sort(5),
                NavigationItem::make(__('Cart'))
                    ->url(function () {
                        return route('checkout.cart');
                    })
                    ->icon('heroicon-o-shopping-bag')
                    ->group(__('Website urls'))
                    ->sort(5),
                NavigationItem::make(__('Products'))
                    ->url(function () {
                        return route('product-list');
                    })
                    ->icon('heroicon-o-rectangle-stack')
                    ->group(__('Website urls'))
                    ->sort(5),
                // NavigationItem::make(__('Product complements'))
                //    ->url(function () {
                //        return route('complement-list');
                //    })
                //    ->icon('heroicon-o-puzzle-piece')
                //    ->group(__('Website urls'))
                //    ->sort(5),
                // NavigationItem::make(__('Product spare parts'))
                //    ->url(function () {
                //        return route('spare-part-list');
                //    })
                //    ->icon('heroicon-s-wrench')
                //    ->group(__('Website urls'))
                //    ->sort(5),
                NavigationItem::make(__('Profile'))
                    ->url('/user/profile', shouldOpenInNewTab: false)
                    ->icon('heroicon-s-user-circle')
                    ->group(__('User'))
                    ->sort(1),

            ])
            ->discoverResources(
                in: app_path('Filament/User/Resources'),
                for: 'App\\Filament\\User\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/User/Pages'),
                for: 'App\\Filament\\User\\Pages'
            )
            ->pages([])
            ->discoverWidgets(
                in: app_path('Filament/User/Widgets'),
                for: 'App\\Filament\\User\\Widgets'
            )
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                RedirectsAdminUsersToAdminPanel::class,
                PushPurchasedItemsToCart::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
