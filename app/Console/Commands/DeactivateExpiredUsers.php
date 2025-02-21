<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class DeactivateExpiredUsers extends Command
{
    protected $signature = 'users:deactivate';
    protected $description = 'Deactivate users whose active_to date has passed';

    public function handle()
    {
        $expiredUsers = User::where('active', true)
                            ->where('active_to', '<', Carbon::now())
                            ->update(['active' => false]);

        $this->info("Deactivated {$expiredUsers} expired users.");
    }
}
