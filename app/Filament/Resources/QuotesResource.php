<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotesResource\Pages;
use App\Filament\Resources\QuotesResource\RelationManagers;
use App\Models\Quotes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\QuotesResource\Api\Transformers\QuotesTransformer;

class QuotesResource extends Resource
{
    protected static ?string $model = Quotes::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Blog Post';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextArea::make('quotes')
                ->required()
                ->maxLength(1000),
            Forms\Components\TextInput::make('author')
                ->required()
                ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quotes')
                    ->limit(20),
                Tables\Columns\TextColumn::make('author'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuotes::route('/create'),
            'edit' => Pages\EditQuotes::route('/{record}/edit'),
        ];
    }

    public static function getApiTransformer()
    {
        return QuotesTransformer::class;
    }
}
