<?php

namespace App\Console\Commands;

use App\Models\Chirp;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('chirper:delete-chirps')]
#[Description('Remove all chirps from the database, using truncate on the table')]
class DeleteAllChirps extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $count = Chirp::withTrashed()->count();
        Chirp::truncate();

        $this->info("Successfully deleted $count chirps from the database.");
    }
}
