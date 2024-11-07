<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Property;
use App\Models\PropertyImage;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Tabs;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\PropertyResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use IbrahimBougaoua\FilamentRatingStar\Actions\RatingStar;
use App\Filament\Resources\PropertyResource\RelationManagers;
use IbrahimBougaoua\FilamentRatingStar\Columns\RatingStarColumn;
use Filament\Forms\Components\TextInput\Mask; // Tambahkan import ini
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;


class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Property';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name_category')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Property'),
                Forms\Components\TextInput::make('short_desc')
                    ->label('Diskripsi Singkat')
                    ->maxLength(255),
                Forms\Components\MarkdownEditor::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->label('Price (IDR)')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('1,000,000')
                    ->afterStateHydrated(function ( Forms\Components\TextInput $component, $state) {
                        if ($state) {
                            $component->state(number_format($state, 0, '.', ','));
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => (int) str_replace(',', '', $state))
                    ->required(),

                Forms\Components\Select::make('period')
                    ->required()
                    ->label('Model Pembayaran')
                    ->options([
                        'onetime' => 'One Time',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                        'weekly' => 'Weekly',
                    ]),




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
                     //->disabled(fn (callable $get) => !$get('city_id')),

                // Forms\Components\TextInput::make('province_id')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('district_id')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('city_id')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('village_id')
                //     ->required()
                //     ->maxLength(255),




                     //Location Coordinates Maps



                    Map::make('location')
                    ->mapControls([
                        'mapTypeControl'    => true,
                        'scaleControl'      => true,
                        'streetViewControl' => true,
                        'rotateControl'     => true,
                        'fullscreenControl' => true,
                        'searchBoxControl'  => false,
                        'zoomControl'       => false,
                    ])
                    ->height(fn () => '400px')
                    ->defaultZoom(5)
                    ->autocomplete('address_autocomplete')
                    // ->afterStateUpdated(function ($state, callable $set) {
                    //     if ($state) {
                    //         $set('address_autocomplete', $state);
                    //     }
                    // })

                    ->reverseGeocode([
                        'street' => '%n %S',
                        'city' => '%L',
                        'state' => '%A1',
                        'zip' => '%z',
                    ])
                    ->debug(true)
                    ->defaultLocation([-6.9887196,110.4565127])
                    ->draggable(true)
                    ->clickable(true)
                    ->geolocate()
                    ->geolocateLabel('Dapatkan Lokasi')
                    ->geolocateOnLoad(true, false)
                    ->layers([
                        'https://googlearchive.github.io/js-v2-samples/ggeoxml/cta.kml',
                    ])
                    ->geoJson('https://fgm.test/storage/AGEBS01.geojson')
                    ->geoJsonContainsField('geojson')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('lat', $state['lat']);
                            $set('lng', $state['lng']);
                            $set('coordinates', $state['lat'] . ',' . $state['lng']);
                        }
                    }),
                    Forms\Components\TextInput::make('address_autocomplete')
                    ->label("Alamat AutoComplete")
                    ->maxLength(255)
                    ->disabled(false)
                    ->dehydrated(false),
                    // ->afterStateUpdated(function ($state, callable $set) {
                    //     $set('address', $state);
                    // }),

                    Forms\Components\TextInput::make('address')
                    ->label("Alamat")
                    ->required()
                    ->maxLength(255)
                    ->disabled(false),
                    // ->afterStateUpdated(function ($state, callable $set) {
                    //     $set('address_autocomplete', $state);
                    // }),



                    Forms\Components\TextInput::make('lat')
                    ->required()
                    ->label("Latitude")
                    ->disabled(false),

                    Forms\Components\TextInput::make('lng')
                    ->required()
                    ->label("Longitude")
                    ->disabled(false),

                    Forms\Components\TextInput::make('coordinates')
                    ->label('Koordinat')
                    ->disabled(false),






            // Nearby
                    Forms\Components\TagsInput::make('nearby')
                    ->nestedRecursiveRules([
                    'min:3',
                    'max:50',
            ]   )
                ->separator(',')
                ->suggestions([
                    'tailwindcss',
                    'alpinejs',
                    'laravel',
                    'livewire',
                ])
                ->reorderable()
                ->splitKeys(['Tab'])
                ->color('success'),
                Forms\Components\Select::make('ads')
                    ->required()
                    ->options([
                        'sell' => 'Sell',
                        'rent' => 'Rent',
                    ]),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'sold' => 'Sold',
                        'rented' => 'Rented',
                        'inactive' => 'Inactive',
                    ]),
                Forms\Components\Toggle::make('featured')
                    ->required(),
                Forms\Components\TextInput::make('meta_title')
                    ->maxLength(255),
                Forms\Components\Textarea::make('meta_description')
                    ->maxLength(65535),

                Forms\Components\TagsInput::make('keywords')
                    ->nestedRecursiveRules([
                        'min:3',
                        'max:50',
                ]   )
                    ->separator(',')
                    ->suggestions([
                        'tailwindcss',
                        'alpinejs',
                        'laravel',
                        'livewire',
                    ])
                    ->reorderable()
                    ->splitKeys(['Tab'])
                    ->color('danger'),

                    RatingStar::make('rating')
                    ->label('Rating')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category.name_category')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->description(fn (Property $record): string => $record->short_desc),
                Tables\Columns\TextColumn::make('price')
                    ->prefix('Rp.')
                    ->color('primary')
                    ->numeric(decimalPlaces:0)
                    ->sortable(),

                Tables\Columns\TextColumn::make('coordinates')
                    ->label('Koordinat')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('address_autocomplete')
                    ->label('Alamat Autocomplete')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->formatStateUsing(function (Property $record) {
                        $location = $record->getLocationAttribute();
                        return "Lat: {$location['lat']}, Lng: {$location['lng']}";
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('lat')
                    ->label('Lat'),

                Tables\Columns\TextColumn::make('lng')
                    ->label('Lng'),


                // Tables\Columns\TextColumn::make('lng')
                //         ->label('Longitude')
                //         ->state(function (Property $record) {
                //             $coordinates = explode(',', $record->coordinates);
                //             return $coordinates[1] ?? null;
                //         })
                //         ->toggleable(isToggledHiddenByDefault: true),



                // MapColumn::make('location')
                //     ->extraAttributes([
                //         'class' => 'property-map'
                //     ])
                //     ->extraImgAttributes(
                //         fn ($record): array => ['title' => $record->lat . ',' . $record->lng]
                //     )
                //     ->height('150')
                //     ->width('250')
                //     ->type('satellite')
                //     ->zoom(15)
                //     ->label('Lokasi')
                //     ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('period')
                ->badge()
                ->label('Period')
                ->colors([
                    'primary' => 'onetime',
                    'success' => 'monthly',
                    'warning' => 'yearly',
                    'danger' => 'weekly',
                ])
                ->searchable(),

                Tables\Columns\TextColumn::make('ads')
                ->badge()
                ->label('Ads')
                ->colors([
                    'success' => 'sell',
                    'info' => 'rent',
                ])
                ->searchable(),

                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->label('Status')
                ->colors([
                    'success' => 'active',
                    'danger' => 'sold',
                    'warning' => 'rented',
                    'secondary' => 'inactive',
                ])
                ->searchable(),


                Tables\Columns\TextColumn::make('province_id')
                    ->label('Wilayah')
                    ->wrap()
                    ->description(fn ($record) =>
                        "{$record->district->name}, {$record->city->name}, {$record->village->name}")
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($q) use ($search) {
                            $q->whereHas('province', function ($subQuery) use ($search) {
                                $subQuery->where('name', 'like', "%{$search}%");
                            })->orWhereHas('district', function ($subQuery) use ($search) {
                                $subQuery->where('name', 'like', "%{$search}%");
                            })->orWhereHas('city', function ($subQuery) use ($search) {
                                $subQuery->where('name', 'like', "%{$search}%");
                            })->orWhereHas('village', function ($subQuery) use ($search) {
                                $subQuery->where('name', 'like', "%{$search}%");
                            });
                        });
                    })
                    ->formatStateUsing(fn ($record) => $record->province->name ?? '-')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            \App\Models\Region::select('name')
                                ->whereColumn('code', 'properties.province_id')
                                ->limit(1),
                            $direction
                        );
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->verticallyAlignStart(),

                Tables\Columns\IconColumn::make('featured')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                RatingStarColumn::make('rating')
                    ->label('Rating')
                    ->size('xs')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Property $record) {
                        try {
                            $record->delete();
                            Notification::make()
                                ->title('Properti Berhasil Dihapus')
                                ->success()
                                ->send();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->title('Penghapusan Gagal')
                                ->body('Properti tidak dapat dihapus karena masih memiliki relasi dengan data lain.')
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            try {
                                $records->each->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->title('Penghapusan Gagal')
                                    ->body('Properti tidak dapat dihapus karena masih memiliki relasi dengan data lain.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('Properti Berhasil Dihapus')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            RelationManagers\ImagesRelationManager::class,
            RelationManagers\SpecificationRelationManager::class,
            RelationManagers\FacilityRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Tabs::make('Tabs')
                ->columnSpan('full')

                ->tabs([
                    Tabs\Tab::make('Info')
                        ->icon('heroicon-m-home')
                        ->schema([
                            TextEntry::make('title')
                            ->label('Judul Iklan')
                            ->color('primary'),
                            TextEntry::make('category.name_category')
                            ->label('Category')
                            ->color('primary'),
                            TextEntry::make('short_desc')
                            ->label('Short Description')
                            ->color('primary'),
                            TextEntry::make('description')
                            ->label('Deskripsi')
                            //->color('primary')
                            ->markdown(),
                            TextEntry::make('price')
                                ->label('Harga')
                                ->prefix('Rp.')
                                ->color('primary')
                                ->numeric(decimalPlaces: 0),
                            TextEntry::make('address')
                            ->color('primary'),
                            TextEntry::make('created_at')
                            ->color('info')
                            ->dateTime(),
                            TextEntry::make('updated_at')
                            ->color('info')
                            ->dateTime(),
                        ])
                        ->columns(2),

                    Tabs\Tab::make('Images')
                        ->icon('heroicon-m-photo')
                        ->schema([
                            RepeatableEntry::make('property_images')
                            ->label('Image URL (Debug)')
                                ->schema([
                                    ImageEntry::make('image_url')

                                    ->columnSpanFull()
                                        //->disk('public')
                                        ->size(200)
                                        //->extraImgAttributes(['alt' => 'Property Image']),
                                ]),
                        ]),

                    Tabs\Tab::make('Specification')
                        ->icon('heroicon-m-tag')
                        ->schema([
                            TextEntry::make('specification.land_size')
                                ->label('Luas Tanah')
                                ->placeholder('empty')
                                ->suffix('m2'),
                            TextEntry::make('specification.building_size')
                                ->label('Luas Bangunan')
                                ->placeholder('empty')
                                ->suffix('m2'),
                            TextEntry::make('specification.bedroom')
                                ->label('Kamar Tidur')
                                ->placeholder('empty'),
                            TextEntry::make('specification.carpot')
                                ->label('Carpot')
                                ->placeholder('empty'),
                            TextEntry::make('specification.bathroom')
                                ->label('Kamar Mandi')
                                ->placeholder('empty'),
                            TextEntry::make('specification.dining_room')
                                ->label('Ruang Makan')
                                ->placeholder('empty'),
                            TextEntry::make('specification.living_room')
                                ->label('Ruang Keluarga')
                                ->placeholder('empty'),
                            TextEntry::make('specification.floors')
                                ->label('Jumlah Lantai')
                                ->placeholder('empty'),
                        ])
                        ->columns(2),

                    Tabs\Tab::make('Facility')
                        ->icon('heroicon-c-clipboard-document-list')
                        ->schema([
                            TextEntry::make('facility.certificate')
                                ->label('Sertificate')
                                ->placeholder('empty'),
                            TextEntry::make('facility.electricity')
                                ->label('Electricity')
                                ->placeholder('empty')
                                ->suffix('kWh'),
                            TextEntry::make('facility.line_phone')
                                ->label('Jaringan Telepon')
                                ->placeholder('empty'),
                            TextEntry::make('facility.internet')
                                ->label('Akses Internet')
                                ->placeholder('empty'),
                            TextEntry::make('facility.road_width')
                                ->label('Lebar Jalan')
                                ->suffix('m2')
                                ->placeholder('empty'),
                            TextEntry::make('facility.water_source')
                                ->label('Sumber Air')
                                ->placeholder('empty'),
                            TextEntry::make('facility.hook')
                                ->label('Hook Jalan')
                                ->placeholder('empty'),
                            TextEntry::make('facility.condition')
                                ->label('Kondisi')
                                ->placeholder('empty'),
                            TextEntry::make('facility.security')
                                ->label('Security')
                                ->placeholder('empty'),
                            TextEntry::make('facility.wastafel')
                                ->label('Wastafel')
                                ->placeholder('empty'),
                        ])
                        ->columns(2),
                ]),
        ]);
}
    public static function getNavigationBadge(): ?string
        {
            return static::getModel()::count();
        }


}
