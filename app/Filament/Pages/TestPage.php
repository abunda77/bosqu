<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TestPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-c-server-stack';
    protected static ?string $navigationGroup = 'Setting';

    protected static string $view = 'filament.pages.test-page';
    protected static ?int $navigationSort = 2;
}
