<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Profile;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\MarkdownEditor;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Illuminate\Database\Eloquent\Model;

class ProfileRelationManager extends RelationManager
{
    protected static string $relationship = 'profile';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('title')
                    ->options([
                        'mr' => 'Mr',
                        'mrs' => 'Mrs',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                PhoneInput::make('phone')
                    ->defaultCountry('ID')

                    ,
                PhoneInput::make('whatsapp')


                    ->defaultCountry('ID')
                    ,

                Forms\Components\Textarea::make('address')
                    ->maxLength(65535),
                    Select::make('province_id')
                     ->label('Provinsi')
                     ->options(function () {
                         return DB::table('regions')
                             ->where('level', 'province')
                             ->pluck('name', 'code');
                     })
                     ->searchable()
                     ->reactive()
                     ->afterStateUpdated(fn (callable $set) => $set('district_id', null)),

                Select::make('district_id')
                     ->label('Kabupaten/Kota')
                     ->options(function (callable $get) {
                         $provinceId = $get('province_id');
                         if (!$provinceId) {
                             return [];
                         }
                         return DB::table('regions')
                             ->where('level', 'district')
                             ->where('code', 'like', $provinceId . '%')
                             ->pluck('name', 'code');
                     })
                     ->searchable()
                     ->reactive()
                     ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),
                     //->disabled(fn (callable $get) => !$get('province_id')),

                Select::make('city_id')
                     ->label('Kecamatan')
                     ->options(function (callable $get) {
                         $districtId = $get('district_id');
                         if (!$districtId) {
                             return [];
                         }
                         return DB::table('regions')
                             ->where('level', 'city')
                             ->where('code', 'like', $districtId . '%')
                             ->pluck('name', 'code');
                     })
                     ->searchable()
                     ->reactive()
                     ->afterStateUpdated(fn (callable $set) => $set('village_id', null)),
                     //->disabled(fn (callable $get) => !$get('district_id')),

                Select::make('village_id')
                     ->label('Desa/Kelurahan')
                     ->options(function (callable $get) {
                         $cityId = $get('city_id');
                         if (!$cityId) {
                             return [];
                         }
                         return DB::table('regions')
                             ->where('level', 'village')
                             ->where('code', 'like', $cityId . '%')
                             ->pluck('name', 'code');
                     })
                     ->searchable(),
                Forms\Components\Select::make('gender')
                    ->options([
                        'man' => 'Man',
                        'woman' => 'Woman',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('birthday'),

                Forms\Components\FileUpload::make('avatar')
                    ->image()
                    ->disk('public')
                    ->optimize('webp')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                    // ->directory('avatars'),

                Forms\Components\TextInput::make('remote_url')
                    ->label('Avatar URL')
                    ->url()
                    ->placeholder('https://example.com/image.jpg')
                    ->helperText('Alternatively, you can provide a URL to an image'),





                Forms\Components\KeyValue::make('social_media')
                    ->label('Social Media')
                    ->keyLabel('Nama Platform')
                    ->keyPlaceholder('Facebook, Instagram, Twitter, etc')
                    ->valueLabel('Nama Account')
                    ->valuePlaceholder('https://facebook.com/username, @INSTGRAM, @tiktok, etc'),
                Forms\Components\TextInput::make('company_name')
                    ->maxLength(255),
                    //MarkdownEditor::make('content')
                Forms\Components\MarkdownEditor::make('biodata_company')
                    ->maxLength(65535)
                    ->columnSpan(2),
            ]);
    }

protected function handleRecordUpdate(Model $record, array $data): Model
{
    if (!empty($data['remote_url'])) {
        $data['avatar'] = Profile::saveImageFromUrl($data['remote_url']);
        unset($data['remote_url']);
    }

    $record->update($data);

    return $record;
}
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\TextColumn::make('first_name'),
                Tables\Columns\TextColumn::make('last_name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->getStateUsing(function ($record) {
                        return $record->avatar ?? $record->remote_url;
                    }),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('gender'),
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
