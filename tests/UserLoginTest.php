<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Attendize\Utils;

class UserTest extends TestCase
{
    /**
     * Test login page is successful
     *
     * @return void
     */
    public function testLogin()
    {
        $this->visit(route('login'))
            ->type($this->test_user_email, 'email')
            ->type($this->test_user_password, 'password')
            ->press('Login')
            ->seePageIs(route('showCreateOrganiser', ['first_run' => '1']));
    }
}
