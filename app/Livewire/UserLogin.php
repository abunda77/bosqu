<?php

namespace App\Livewire;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

use Livewire\Component;

class UserLogin extends Component
{


    public function render()
    {
        return view('livewire.user-login');
    }
}
