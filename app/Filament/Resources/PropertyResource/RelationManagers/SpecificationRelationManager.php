<?php
namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class SpecificationRelationManager extends RelationManager
{
    protected static string $relationship = 'specification';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('land_size')
                    ->required()
                    ->numeric()
                    ->label('Luas Tanah (m²)')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->suffix('m²')
                    ->placeholder('1,000')
                    ->afterStateHydrated(function ( Forms\Components\TextInput $component, $state) {
                        if ($state) {
                            $component->state(number_format($state, 0, '.', ','));
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => (int) str_replace(',', '', $state)),

                Forms\Components\TextInput::make('building_size')
                    ->required()
                    ->numeric()
                    ->label('Luas Bangunan (m²)')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->suffix('m²')
                    ->placeholder('1,000')
                    ->afterStateHydrated(function ( Forms\Components\TextInput $component, $state) {
                        if ($state) {
                            $component->state(number_format($state, 0, '.', ','));
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => (int) str_replace(',', '', $state)),

                Forms\Components\TextInput::make('bedroom')
                    ->label('Jumlah Kamar Tidur')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('bathroom')
                    ->label('Jumlah Kamar Mandi')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('floors')
                ->label('Jumlah Lantai')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('land_size')
            ->columns([
                Tables\Columns\TextColumn::make('land_size')
                ->label('Luas Tanah (m²)')
                ->numeric(decimalPlaces: 0),
                Tables\Columns\TextColumn::make('building_size')
                ->label('Luas Bangunan (m²)')
                ->numeric(decimalPlaces: 0),

                Tables\Columns\TextColumn::make('bedroom')
                ->label('Jumlah Kamar Tidur'),
                Tables\Columns\TextColumn::make('bathroom')
                ->label('Jumlah Kamar Mandi'),
                Tables\Columns\TextColumn::make('floors')
                ->label('Jumlah Lantai'),
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
