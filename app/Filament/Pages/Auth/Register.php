<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as AuthRegister;
use Filament\Pages\Page;

use function Laravel\Prompts\form;

class Register extends AuthRegister
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

    }



}
