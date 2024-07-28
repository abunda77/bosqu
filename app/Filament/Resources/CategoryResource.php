<?php

namespace App\Filament\Resources;


use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\PropertiesRelationManager;

use App\Enums\PropertyCategory; // Pastikan path ini sesuai dengan lokasi file enum Anda

use Filament\Support\Contracts\HasLabel;




class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Property';
    protected static ?string $recordTitleAttribute = 'name_category';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('name_category')
                ->required()
                ->options([
                    'home' => 'Rumah',
                    'warehouse' => 'Gudang',
                    'apartement' => 'Apartemen',
                    'homeshop' => 'Ruko',
                    'kavling' => 'Kavling',
                    'office' => 'Office',
                ])
                    ->reactive() // Make the field reactive to update other fields based on its value
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Automatically set the slug field based on the name_category input
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('icon_url')
                    ->optimize('webp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name_category')
                ->badge()
                ->searchable()
                //->formatStateUsing(fn (PropertyCategory $state): string => $state->getLabel()),
                ->label('Name Category')
                ->getStateUsing(function ($record) {
                    $labels = [
                        'home' => 'Rumah',
                        'warehouse' => 'Gudang',
                        'apartement' => 'Apartemen',
                        'homeshop' => 'Ruko',
                        'kavling' => 'Kavling',
                        'office' => 'Kantor',
                    ];

                    return $labels[$record->name_category ?? $record->name_category];
                })
                ->color(fn (string $state): string => match ($state) {
                    'Rumah' => 'success',
                    'Gudang' => 'danger',
                    'Apartemen' => 'warning',
                    'Ruko' => 'info',
                    'Kavling' => 'primary',
                    'Kantor' => 'gray',
                    default => 'default',
                }),
                //->formatStateUsing(fn (PropertyCategory $state): string => $state->getLabel()),

                Tables\Columns\ImageColumn::make('icon_url')
                ->label('Icon')
                    ->circular()
                    ->placeholder('empty')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //PropertiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
        {
            return static::getModel()::count();
        }

}
