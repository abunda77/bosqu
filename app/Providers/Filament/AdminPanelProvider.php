<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Pages\TestPage;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\UserOverview;
use Rupadana\ApiService\ApiServicePlugin;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Joaopaulolndev\FilamentEditEnv\FilamentEditEnvPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    protected static ?string $navigationLabel = 'Custom Navigation Label';

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->font('Montserrat')
            ->authGuard('admin')
            ->authPasswordBroker('admin')
            ->brandName('Bosque Properti')
            ->brandLogo(asset('images/logo.png'))
            ->darkModeBrandLogo(asset('images/dark-logo.png'))
            ->brandLogoHeight('4rem')
            ->unsavedChangesAlerts()
            ->favicon(asset('images/favicon.png'))
            ->login()
            ->profile()
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn() => view('footer')
            )
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Edit profile'),
                MenuItem::make()
                    ->label('Settings')
                    ->url(fn (): string => TestPage::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
                'logout' => MenuItem::make()->label('Log out'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                UserOverview::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                'Property',
                'Member',
                'Blog Post',
                'Setting',
                'Blog',
                'Statistic',
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                ApiServicePlugin::make(),
                FilamentEditEnvPlugin::make()
                    ->showButton(fn () => Auth::guard('admin')->user()?->id === 1)
                    ->setIcon('heroicon-o-cog'),
            ]);
    }
}
