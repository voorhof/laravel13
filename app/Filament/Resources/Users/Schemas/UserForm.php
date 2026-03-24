<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
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
            ]);
    }
}
