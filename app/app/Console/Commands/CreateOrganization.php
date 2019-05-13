<?php

namespace App\Console\Commands;

use App\Organization;
use Illuminate\Console\Command;

class CreateOrganization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'organization:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new organization';

    /**
     * Create a new command instance.
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
        $result = Organization::create([
            'full_name' => $this->argument('name'),
        ]);
        $this->info('Organization added with ID '.$result['id']);
    }
}
