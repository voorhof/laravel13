<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(function ($operation): bool {
                        // Only autofocus the first field on creation, not on the edit page.
                        return $operation === Operation::Create->value;
                    }),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->unique()
                    ->required()
                    ->maxLength(255)
                    ->autocomplete(false),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->hiddenOn(Operation::Edit)
                    ->autocomplete('new-password'),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Choose role')
                    ->disableOptionWhen(fn (string $label): bool => $label === 'Super Admin')
                    ->visible(fn (): bool => auth()->check() && auth()->user()->can('update roles')),
            ]);
    }
}
