<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Attendize\Utils;

class UserTest extends TestCase
{
    /**
     * Test user edit page is successful
     *
     * @return void
     */
    public function test_edit_user_is_successful()
    {
        $this->actingAs($this->test_user);

        $organiser = factory(App\Models\Organiser::class)->create(['account_id' => 1]);

        $server = array('HTTP_X-Requested-With' => 'XMLHttpRequest');

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = 'new@email.com.au';
        $post = array(
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        );

        $this->call('post', route('postEditUser'), $post, $server);

        $this->seeJson([
            'status' => 'success',
            'message' => 'Successfully Edited User',
        ]);

        $user = App\Models\User::find($this->test_user->id);

        $this->assertEquals($firstName, $user->first_name);
        $this->assertEquals($lastName, $user->last_name);
        $this->assertEquals($email, $user->email);
    }
}
