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
                Forms\Components\Select::make('hook')
                    ->options([
                        'yes' => 'Yes',
                        'no' => 'No',
                    ])
                    ->required()
                    ->label('Hook'),
                Forms\Components\Select::make('condition')
                    ->options([
                        'very_good' => 'Sangat Baik',
                        'good' => 'Baik',
                        'semi_good' => 'Cukup Baik',
                        'average' => 'Rata-rata',
                        'not_good' => 'Kurang Baik',
                        'bad' => 'Buruk',
                        'very_bad' => 'Sangat Buruk',
                    ])
                    ->required()
                    ->label('Kondisi'),
                Forms\Components\Select::make('security')
                    ->options([
                        'yes' => 'Ya',
                        'no' => 'Tidak',
                    ])
                    ->required()
                    ->label('Keamanan'),
                Forms\Components\Select::make('wastafel')
                    ->options([
                        'yes' => 'Ya',
                        'no' => 'Tidak',
                    ])
                    ->required()
                    ->label('Wastafel'),
                Forms\Components\TextInput::make('internet')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('road_width')
                    ->maxLength(255)
                    ->suffix('meter')
                    ->required()
                    ->label('Lebar Jalan (meter)'),
                Forms\Components\TextInput::make('water_source')
                    ->required()
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
                Tables\Columns\TextColumn::make('hook')
                    ->formatStateUsing(fn (string $state): string => $state === 'yes' ? 'Ya' : 'Tidak'),
                Tables\Columns\TextColumn::make('condition')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'very_good' => 'Sangat Baik',
                        'good' => 'Baik',
                        'semi_good' => 'Cukup Baik',
                        'average' => 'Rata-rata',
                        'not_good' => 'Kurang Baik',
                        'bad' => 'Buruk',
                        'very_bad' => 'Sangat Buruk',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('security')
                    ->formatStateUsing(fn (string $state): string => $state === 'yes' ? 'Ya' : 'Tidak'),
                Tables\Columns\TextColumn::make('wastafel')
                    ->formatStateUsing(fn (string $state): string => $state === 'yes' ? 'Ya' : 'Tidak'),
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
