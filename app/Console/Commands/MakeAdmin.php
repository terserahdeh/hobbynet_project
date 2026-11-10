<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MakeAdmin extends Command
{
    protected $signature = 'admin:make {email : Email of an existing user} {--revoke : Remove admin access instead}';

    protected $description = 'Give (or take away) admin access for a user';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $grant = ! $this->option('revoke');

        $found = DB::table('users')->where('email', $email)->exists();

        if (! $found) {
            $this->error("No user with the email {$email}.");

            return self::FAILURE;
        }

        DB::table('users')->where('email', $email)->update(['is_admin' => $grant]);

        $this->info($grant ? "{$email} is now an admin." : "{$email} is no longer an admin.");

        return self::SUCCESS;
    }
}
