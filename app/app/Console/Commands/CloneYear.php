<?php

namespace App\Console\Commands;

use App\Models\Year;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class CloneYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'year:clone {year_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone an organization\'s year data';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param mixed $description
     *
     * @return CarbonImmutable
     */
    public function ask_date($description)
    {
        $date = null;
        while (!$date) {
            $date_str = $this->ask(sprintf('[%s] Enter a date (yyyy-mm-dd)', $description));
            $date = CarbonImmutable::createFromFormat('Y-m-d', $date_str);
            if (!$date) {
                $this->warn('Invalid date. Try again.');
            }
        }

        return $date;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = Year::findOrFail($this->argument('year_id'));
        $organization_id = $this->ask('Organization?');
        $title = $this->ask('Year title?');
        $description = $this->ask('Year description?');
        // get first date
        $first_day = $this->ask_date('First day');
        // get last date
        $last_day = $this->ask_date('Last day');
        // get list of exception days
        $exception_days = [];
        while ('y' == $this->ask('Do you want to add an exception day (remove a date from the calendar)? [yn]')) {
            $exception_days[] = $this->ask_date('Exception day');
        }
        // will use same week days
        $new_year = $year->make_copy($organization_id, $title, $description, $first_day, $last_day, $exception_days);
        $this->info('Year cloned. New year id: '.$new_year->id);
    }
}
