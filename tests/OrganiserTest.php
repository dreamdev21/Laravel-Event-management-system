<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Organiser;

class OrganiserTest extends TestCase
{
    public function testCreateOrganiser()
    {
        $this->actingAs($this->test_user)
            ->visit(route('showCreateOrganiser'))
            ->type($this->faker->name, 'name')
            ->type($this->faker->email, 'email')
            ->type($this->faker->email, 'about')
            ->type($this->faker->word, 'facebook')
            ->type($this->faker->word, 'twitter')
            ->press('Create Organiser')
            ->seeJson([
                'status' => 'success'
            ]);
    }

    public function testEditOrganiser()
    {
        $organiser = factory(App\Models\Organiser::class)->create();

        $this->actingAs($this->test_user)
            ->visit(route('showOrganiserCustomize', ['organiser_id' => $organiser->id]))
            ->type($this->faker->name, 'name')
            ->type($this->faker->email, 'email')
            ->type($this->faker->email, 'about')
            ->type($this->faker->word, 'facebook')
            ->type($this->faker->word, 'twitter')
            ->press('Create Organiser')
            ->seeJson([
                'status' => 'success'
            ]);
    }
}
