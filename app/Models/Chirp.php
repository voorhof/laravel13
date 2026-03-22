<?php

namespace App\Models;

use App\Policies\ChirpPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['message'])]
#[UsePolicy(ChirpPolicy::class)]
class Chirp extends Model
{
    use SoftDeletes;

    /**
     * Get the User that owns the Chirp.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
