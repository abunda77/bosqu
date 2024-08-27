<?php
namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Tables\Columns\TextColumnColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected $casts = [
        'image_url' => 'array',
    ];

    public function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_url')
                    ->required()
                    // ->multiple(true)
                    ->optimize('webp')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->default([])
                    // ->rules(['array'])
                    ->maxSize(2048),
                Forms\Components\Toggle::make('is_primary')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('image')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('property_id'),
                Tables\Columns\ImageColumn::make('image_url'),
                Tables\Columns\IconColumn::make('is_primary')
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('is_primary')

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
