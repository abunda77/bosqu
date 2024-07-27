<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FacilityRelationManager extends RelationManager
{
    protected static string $relationship = 'facility';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('certificate')
                    ->options([
                        'shm' => 'SHM',
                        'shgb' => 'SHGB',
                        'shp' => 'SHP',
                        'shgu' => 'SHGU',
                        'shmsrs' => 'SHMSRS',
                        'sta' => 'STA',
                    ])
                    ->required()
                    ->label('Status Kepemilikan'),
                Forms\Components\TextInput::make('electricity')
                    ->required()
                    ->numeric()
                    ->suffix('kWh'),
                Forms\Components\Select::make('line_phone')
                    ->options([
                        'yes' => 'Yes',
                        'no' => 'No',
                        'progress' => 'In Progress',
                    ])
                    ->required()
                    ->label('Jalur Telepon'),
                Forms\Components\TextInput::make('internet')
                    ->maxLength(255),
                Forms\Components\TextInput::make('road_width')
                    ->maxLength(255)
                    ->suffix('meter')
                    ->label('Lebar Jalan (meter)'),
                Forms\Components\TextInput::make('water_source')
                    ->label('Sumber Air')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('certificate')
            ->columns([
                Tables\Columns\TextColumn::make('certificate'),
                Tables\Columns\TextColumn::make('electricity'),
                Tables\Columns\TextColumn::make('line_phone'),
                Tables\Columns\TextColumn::make('internet'),
                Tables\Columns\TextColumn::make('water_source'),
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
