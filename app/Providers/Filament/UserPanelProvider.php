<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Register;
use Filament\Http\Livewire\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
#use Filament\Pages\Auth\Register;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
//use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use App\Filament\Pages\Auth\RequestPasswordReset;


class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->login()
            ->brandName('Bosque Properti')
            ->brandLogo(asset('images/logo.png'))
            ->darkModeBrandLogo(asset('images/dark-logo.png'))
            ->brandLogoHeight('4rem')
            //->unsavedChangesAlerts()
            ->favicon(asset('images/favicon.png'))
            ->authGuard('web')
            ->authPasswordBroker('users')
            ->passwordReset(RequestPasswordReset::class)
            //->spa()


            ->emailVerification(EmailVerificationPrompt::class)
            ->profile(EditProfile::class)
            ->registration(Register::class)
            //->registration()
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
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
            ]);
    }
}
