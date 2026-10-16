<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'chargegrid:make-admin {phone : Phone number of an existing account}';
    protected $description = 'Grant administrator access to an existing ChargeGrid account';

    public function handle(): int
    {
        $user = User::where('phone', $this->argument('phone'))->first();
        if (!$user) {
            $this->error('Account not found. Create the account first.');
            return self::FAILURE;
        }
        if (!$this->confirm('Grant administrator access to '.$user->name.' ('.$user->phone.')?')) {
            $this->warn('No changes made.');
            return self::SUCCESS;
        }
        $user->forceFill(['is_admin' => true])->save();
        $this->info('Administrator access granted.');
        return self::SUCCESS;
    }
}
