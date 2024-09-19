<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\PostResource\Api\Transformers\PostTransformer;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Blog Post';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', \Illuminate\Support\Str::slug($state));
                    })
                    ->debounce(1000),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                Forms\Components\FileUpload::make('feature_image')
                    ->label('Gambar Fitur')
                    ->image()


                    ->optimize('webp')
                    ->acceptedFileTypes(['image/jpeg', 'image/jpg','image/png', 'image/webp'])
                    ,

                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'private' => 'Private',
                    ]),
                Forms\Components\RichEditor::make('body')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\Select::make('admin_id')
                    ->relationship('admin', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id')
                //     ->label('ID'),

                Tables\Columns\TextColumn::make('title'),




                // Tambahkan colomn body
                Tables\Columns\TextColumn::make('body')
                    ->label('Body')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(50),

                // tambahkan colomn feature_image
                Tables\Columns\ImageColumn::make('feature_image')
                    ->label('Feature Image')
                    ->circular(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->colors([
                        'primary' => 'draft',
                        'success' => 'published',
                        'warning' => 'private',
                    ]),
                // Tables\Columns\TextColumn::make('body'),
                // Tables\Columns\TextColumn::make('admin.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\TagsRelationManager::class,
            // RelationManagers\CommentsRelationManager::class,
            // RelationManagers\ImagesRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getApiTransformer()
    {
        return PostTransformer::class;
    }
}
