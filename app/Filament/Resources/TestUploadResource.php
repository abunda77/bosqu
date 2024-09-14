<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestUploadResource\Pages;
use App\Filament\Resources\TestUploadResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\TestUpload;

class TestUploadResource extends Resource
{
    protected static ?string $model = TestUpload::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('upload_url')

                    ->image()
                    ->optimize('webp')
                    ->maxSize(5120) // 5MB
                    ->acceptedFileTypes(['image/*']),

                Forms\Components\TextInput::make('remote_url')
                    ->label('Alternative')
                    ->url()
                    ->placeholder('https://example.com/image.jpg')
                    ->helperText('Alternatively, you can provide a URL to an image'),
            ]);
    }


protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (!empty($data['remote_url'])) {
            $data['upload_url'] = TestUpload::saveImageFromUrl($data['remote_url']);
            unset($data['remote_url']);
        }

        $record->update($data);

        return $record;
    }


    public static function table(Table $table): Table

    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('upload_url')
                    ->size(100)
                    ->circular()
                    ->getStateUsing(function ($record) {
                        return $record->upload_url ?? $record->remote_url;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                ])
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
            'index' => Pages\ListTestUploads::route('/'),
            'create' => Pages\CreateTestUpload::route('/create'),
            'edit' => Pages\EditTestUpload::route('/{record}/edit'),
        ];
    }
}
