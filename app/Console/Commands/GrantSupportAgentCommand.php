<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GrantSupportAgentCommand extends Command
{
    protected $signature = 'chat:grant-support {email : Email address of an existing user} {--revoke : Remove support access instead}';

    protected $description = 'Grant or revoke access to the DocuFlow support conversation inbox';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) $this->argument('email')));
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $this->error("No user account exists for {$email}.");
            $this->line('Register and verify the account first, then run this command again.');

            return self::FAILURE;
        }

        $grantAccess = ! $this->option('revoke');
        $user->forceFill(['is_support_agent' => $grantAccess])->save();

        $this->info($grantAccess
            ? "Support inbox access granted to {$email}."
            : "Support inbox access revoked from {$email}.");

        return self::SUCCESS;
    }
}
