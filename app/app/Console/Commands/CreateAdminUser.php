<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {organization_id} {name} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $password = null;
        while (true) {
            $password = $this->secret('Password?');
            if ($password !== $this->secret('Confirm?')) {
                $this->info('Incorrect.');
                continue;
            }
            break;
        }
        $hint = $this->ask('Hint?');
        User::create([
            "name" => $this->argument('name'),
            "email" => $this->argument('email'),
            "password" => Hash::make($password), // HASH!
            "remember_token" => $hint,
            "admin" => true,
            "organization_id" => $this->argument('organization_id')
        ]);
        $this->info("User added!");
    }
}
