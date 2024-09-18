<?php
namespace App\Filament\Resources\PropertyResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use App\Models\PropertyImage;
use Illuminate\Database\Eloquent\Model;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    // protected $casts = [
    //     'image_url' => 'array',
    // ];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image_url')

                    // ->multiple(true)
                    ->optimize('webp')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->default([])
                    // ->directory('property_images')
                    // ->rules(['array'])
                    ->maxSize(2048),

                Forms\Components\TextInput::make('image_remote_url')
                    ->label('Image URL')
                    ->url()
                    ->placeholder('https://example.com/image.jpg')
                    ->helperText('Alternatively, you can provide a URL to an image'),

                Forms\Components\Toggle::make('is_primary')
                    ->required(),
            ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (!empty($data['image_remote_url'])) {
            $data['image_url'] = PropertyImage::saveImageFromUrl($data['image_remote_url']);
            unset($data['image_remote_url']);
        }

        $record->update($data);

        return $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('image')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('property_id'),
                Tables\Columns\ImageColumn::make('image_url')
                    -> circular()
                    ->getStateUsing(function ($record) {
                        return $record->image_url ?? $record->image_remote_url;
                    }),

                Tables\Columns\IconColumn::make('is_primary')
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('is_primary'),
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
