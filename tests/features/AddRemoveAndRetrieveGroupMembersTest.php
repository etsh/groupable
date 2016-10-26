<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;
use App\School;

class AddRemoveAndRetrieveGroupMembersTest extends TestCase
{
    use DatabaseMigrations;

    private function build()
    {
        // Create the model structure.
        $this->user = User::create([
            'name' => 'John Lennon',
            'email' => 'john@example.com',
            'password' => 'secret',
        ]);

        $this->school = School::create([
            'name' => 'Standard Academy',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function add_user_to_group()
    {
        // Given:
        $this->build();

        // When:
        $this->school->join($this->user);

        // Then:
        $this->seeInDatabase('groupable_members', [
            'group_id' => $this->school->id,
            'group_type' => get_class($this->school),
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function remove_user_from_group()
    {
        // Given:
        $this->build();
        $this->school->join($this->user);
        $this->seeInDatabase('groupable_members', [
            'group_id' => $this->school->id,
            'group_type' => get_class($this->school),
            'user_id' => $this->user->id,
        ]);

        // When:
        $this->school->leave($this->user);

        // Then:
        $this->dontSeeInDatabase('groupable_members', [
            'group_id' => $this->school->id,
            'group_type' => get_class($this->school),
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function retrieve_group_members()
    {
        // Given:
        $this->build();
        $this->school->join($this->user);
        $this->seeInDatabase('groupable_members', [
            'group_id' => $this->school->id,
            'group_type' => get_class($this->school),
            'user_id' => $this->user->id,
        ]);

        // When:
        $result = $this->school->members();

        // Then:
        $this->assertEquals(1, $result->count());
    }
}
