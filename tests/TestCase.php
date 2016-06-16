<?php

use App\Models\Timezone;
use App\Models\User;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost/public';

    /**
     * Email for the test user
     *
     * @var string
     */
    protected $test_user_email = 'test@test.test';

    /**
     * Password for the test user
     *
     * @var string
     */
    protected $test_user_password = 'testtest';

    /**
     * Our test user
     *
     * @var
     */
    protected $test_user;

    /**
     * Our faker instance
     *
     * @var
     */
    protected $faker;


    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->loadEnvironmentFrom('.env.testing');
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    public function setUp(){
        parent::setUp();

        /*
         * Set up faker
         */
        $this->faker = Faker\Factory::create();

        /*
         * Migrate & Seed the DB
         */
        Artisan::call('migrate');
        if (Timezone::count() == 0) {
            Artisan::call('db:seed', ['--force' => true]);
        }

        /*
         * Set up our test user
         */
        if(User::where('email','=','test@test.test')->count() === 0) {
            $this->test_user = factory(App\Models\User::class)->create([
                'email'    => $this->test_user_email,
                'password' => Hash::make($this->test_user_password),
            ]);
        } else {
            $this->test_user = User::where('email','=','test@test.test')->first();
        }

    }

    public function tearDown(){
        parent::tearDown();
    }
}