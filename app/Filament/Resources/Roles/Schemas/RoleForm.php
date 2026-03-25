<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique()
                    ->maxLength(64)
                    ->autofocus(function ($operation): bool {
                        // Only autofocus the first field on creation, not on the edit page.
                        return $operation === Operation::Create->value;
                    }),
                TextInput::make('guard_name')
                    ->required()
                    ->maxLength(64)
                    ->default('web')
                    ->disabled(),
                CheckboxList::make('permissions')
                    ->relationship(titleAttribute: 'name')
                    ->columns()
                    ->searchable()->bulkToggleable(),
            ]);
    }
}
