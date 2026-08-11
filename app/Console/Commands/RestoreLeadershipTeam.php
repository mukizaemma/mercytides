<?php

namespace App\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class RestoreLeadershipTeam extends Command
{
    protected $signature = 'mercytides:restore-leadership';

    protected $description = 'Restore soft-deleted leadership team rows and reseed the default Magambo leadership profiles';

    public function handle(): int
    {
        if (! Schema::hasTable('teams')) {
            $this->error('The teams table does not exist.');

            return self::FAILURE;
        }

        Team::ensureLeadershipSeeded();

        foreach (Team::query()->orderBy('id')->get() as $member) {
            $this->line("{$member->id}. {$member->names} — {$member->position}");
        }

        $this->info('Active leadership members: '.Team::query()->count());

        return self::SUCCESS;
    }
}
