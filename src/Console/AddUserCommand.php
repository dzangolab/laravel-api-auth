<?php

namespace Dzangolab\Auth\Console;

use Dzangolab\Auth\Exceptions\UserAlreadyExistsException;
use Dzangolab\Auth\Services\AuthUserService;
use Exception;
use Illuminate\Console\Command;

class AddUserCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new user that can be used for authentication';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dzangolab-auth:add-user {--dry-run=1} {email} {password} {username?}';

    /**
     * User repository to persist user in database.
     *
     * @var AuthUserService
     */
    protected $userService;

    /**
     * Create a new command instance.
     */
    public function __construct(AuthUserService $userService)
    {
        parent::__construct();

        $this->userService = $userService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $username = $this->argument('username');

        $useUsernameSameAsEmail = (bool) config('auth.username_same_as_email');

        if ($useUsernameSameAsEmail) {
            $username = $email;
        }

        $user = null;

        $this->info(sprintf(
            'User with email %s will be created',
            $email
        ));

        // takes `php artisan app:command --dry-run` as dry run true
        if (null === $dryRun || $dryRun) {
            $this->warn(sprintf(
                'No changes done. Run with --dry-run=0 again to apply changes.'
            ));

            return 0;
        }

        try {
            $user = $this->getAuthUserService()->createUser(
                [
                    'email' => $email,
                    'password' => $password,
                    'username' => $username,
                ]
            );
        } catch (UserAlreadyExistsException $exception) {
            $this->error($exception->getMessage());
        }

        if (!$user) {
            $this->info('Failed to create user.');

            return 1;
        }

        $this->info(sprintf('User was created with id %s', $user->id));

        return 0;
    }

    protected function getAuthUserService(): AuthUserService
    {
        return $this->userService;
    }
}
