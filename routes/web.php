<?php

use Illuminate\Support\Facades\Route;
//use App\Filament\Pages\UserLogin;
use App\Livewire\UserLogin;
use App\Livewire\AboutUs;
use App\Livewire\ContactUs;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/customerlogin', UserLogin::class)->name('customerlogin');
Route::get('/about', AboutUs::class)->name('aboutus');
Route::get('/contact', ContactUs::class)->name('contactus');
