<?php

namespace App\Models;

use App\Policies\RolePolicy;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

#[Fillable(['name', 'guard_name'])]
#[UsePolicy(RolePolicy::class)]
class Role extends SpatieRole
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory;
}
