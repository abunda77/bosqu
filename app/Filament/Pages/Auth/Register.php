<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Events\UserRegistered;
use function Laravel\Prompts\form;
use Illuminate\Support\Facades\Hash;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;


class Register extends BaseRegister
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    //protected static string $view = 'filament.pages.auth.register';
    public function form(Form $form): Form
    {

            return $form->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                TextInput::make('role')
                ->default('customer')
                ->readOnly('customer'),


                #Select::make('role')->default('customer')->options(["superadmin" => "Super Admin", "admin" => "Admin", "operator" => "Operator", "customer" => "Customer"]),
            ])
            ->statePath('data');
            $data = $this->form->getState();

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'],
                ]);
        event(new UserRegistered($user));

        $this->sendEmailVerificationNotification($user);

        Notification::make()
            ->title('Registration successful')
            ->success()
            ->send();

        return app(RegistrationResponse::class);

    }

    // public function register(): ?RegistrationResponse
    // {
    //     $data = $this->form->getState();

    //     $user = User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //         'role' => $data['role'],
    //     ]);

    //     event(new UserRegistered($user));

    //     $this->sendEmailVerificationNotification($user);

    //     Notification::make()
    //         ->title('Registration successful')
    //         ->success()
    //         ->send();

    //     return app(RegistrationResponse::class);
    // }

    // protected function sendEmailVerificationNotification(User $user): void
    // {
    //     if (config('filament.auth.email_verification', false)) {
    //         $user->sendEmailVerificationNotification();
    //     }
    // }
}

