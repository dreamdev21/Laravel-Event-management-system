<?php

namespace App\Console\Commands;

use App\Models\Timezone;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendize:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install of Attendize';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $version = file_get_contents(base_path('VERSION'));
        try {
            DB::connection();
        } catch (\Exception $e) {
            $this->error('Unable to connect to database.');
            $this->error('Please fill valid database credentials into .env and rerun this command.');

            return;
        }

        $this->comment('Attempting to install Attendize v'.$version);

        if (!env('APP_KEY')) {
            $this->info('Generating app key');
            Artisan::call('key:generate');
        } else {
            $this->comment('App key exists -- skipping');
        }

        $this->info('Migrating database');
        Artisan::call('migrate', ['--force' => true]);

        if (!Timezone::count()) {
            $this->info('Seeding DB data');
            Artisan::call('db:seed', ['--force' => true]);
        } else {
            $this->comment('Data already seeded -- skipping');
        }

        file_put_contents(base_path('installed'), $version);

        $this->comment('Success! You can now run Attendize');
        $this->comment('Navigate to /signup to create your account.');
    }
}
