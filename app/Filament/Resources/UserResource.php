<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Profile;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder; //
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    protected static ?string $navigationGroup = 'Member';

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Members';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Fieldset::make('Password Update')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                            ->nullable()
                            ->dehydrated(fn ($state) => filled($state)),
                    ]),
                Select::make('role')
                    ->required()
                    ->options([
                        'superadmin' => 'Super Admin',
                        'admin' => 'Admin',
                        'operator' => 'Operator',
                        'customer' => 'Customer',
                    ]),
                Toggle::make('is_active')
                    ->required(),
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



    public static function table(Table $table): Table
    {
        return $table
            ->heading('Management User')
            ->description('Manage your users here.')
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Username'),

                Tables\Columns\TextColumn::make('profile.avatar')
                    // ->circular()
                    // ->disk('public')
                    // ->placeholder('kosong')
                    ->getStateUsing(function ($record) {
                        return $record->profile->avatar ?? $record->profile->remote_url ?? null;
                    }),
                    // ->getStateUsing(function ($record) {
                    //     return $record->avatar ?? $record->remote_url;
                    // }),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('gray')
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('profile', function (Builder $query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('profile.first_name', $direction)
                            ->orderBy('profile.last_name', $direction);
                    })
                    ->getStateUsing(function ($record) {
                        if ($record->profile) {
                            return $record->profile->first_name.' '.$record->profile->last_name;
                        }

                        return null;
                    }),
                // Tables\Columns\TextColumn::make('profile.first_name')
                //     ->searchable()
                //     ->label('First Name'),
                // Tables\Columns\TextColumn::make('profile.last_name')
                //     ->searchable()
                //     ->label('Last Name'),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->label('Status')
                    ->colors([
                        'success' => 'superadmin',
                        'danger' => 'admin',
                        'warning' => 'operator',
                        'secondary' => 'customer',
                    ])
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('profile.first_name')
                    ->label('Nama Depan')
                    ->color('primary'),
                ImageEntry::make('profile.avatar')
                    ->label('Avatar')
                    ->placeholder('empty')
                    ->circular()
                    ->getStateUsing(function ($record) {
                        return $record->profile->avatar ?? $record->profile->remote_url;
                    }),
                TextEntry::make('profile.email')
                    ->label('Email')
                    ->placeholder('empty')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary')
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500),
                TextEntry::make('profile.phone')
                    ->label('No. Telepon')
                    ->placeholder('empty')
                    ->icon('heroicon-s-phone')

                    ->iconColor('primary')
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('No.Phone copied')
                    ->copyMessageDuration(1500)                    ,
                TextEntry::make('profile.whatsapp')
                    ->label('Whatsapp')
                    ->placeholder('empty')
                    ->color('primary')
                    ->icon('heroicon-m-device-phone-mobile')
                    // ->icon('heroicon-m-device-phone-mobile')
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Whatsapp copied')
                    ->copyMessageDuration(1500),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProfileRelationManager::class,
            RelationManagers\PropertiesRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['profile.first_name', 'profile.last_name'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
