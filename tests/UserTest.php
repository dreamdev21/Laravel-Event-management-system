<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSignup()
    {
        $this->visit(route('showSignup'))
            ->type('Joe', 'first_name')
            ->type('Blogs', 'last_name')
            ->type($this->faker->email, 'email')
            ->type('password', 'password')
            ->type('password', 'password_confirmation')
            ->check('terms_agreed')
            ->press('Sign Up')
            ->seePageIs(route('login'));
    }
    
    public function testLogin()
    {
        $this->visit(route('login'))
            ->type($this->test_user_email, 'email')
            ->type($this->test_user_password, 'password')
            ->press('Login')
            ->seePageIs(route('showCreateOrganiser', ['first_run' => '1']));
    }

}
