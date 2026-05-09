<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('resources.nav.groups.system');
    }

    public static function getModelLabel(): string
    {
        return __('resources.user.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.user.plural_label');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('resources.fields.name'))
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label(__('resources.fields.email'))
                ->email()
                ->required()
                ->unique(User::class, 'email', ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('password')
                ->label(__('resources.fields.password'))
                ->password()
                ->revealable()
                ->required(fn (string $operation) => $operation === 'create')
                ->dehydrated(fn (?string $state) => filled($state))
                ->minLength(8)
                ->maxLength(255)
                ->helperText(fn (string $operation) => $operation === 'edit'
                    ? __('resources.user.password_helper')
                    : null),

            Select::make('roles')
                ->label(__('resources.user.roles'))
                ->multiple()
                ->relationship('roles', 'name')
                ->options(
                    Role::all()->pluck('name', 'id')
                )
                ->preload()
                ->searchable()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('resources.fields.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('resources.fields.email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('resources.user.roles'))
                    ->badge()
                    ->separator(', '),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('resources.fields.created_at'))
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label(__('resources.user.roles'))
                    ->relationship('roles', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (User $record) {
                        // Prevent deleting yourself
                        if ($record->id === auth()->id()) {
                            abort(403, 'Cannot delete your own account.');
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
