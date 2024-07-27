<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

// class DashboardPage extends \Filament\Pages\Dashboard
class DashboardPage extends Page

{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Setting';
    protected static string $view = 'filament.pages.dashboard-page';
}
