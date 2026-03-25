<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Permission;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->name === 'Admin') {
            $this->record->syncPermissions(Permission::where('guard_name', $this->record->guard_name)->get());
        }
    }
}
