<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;
use App\School;

class AddAndRemoveGroupMemberRolesTest extends TestCase
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

        $this->school->join($this->user);
    }

    /** @test */
    public function grant_member_role()
    {
        // Given:
        $this->build();

        // When:
        $this->school->grant($this->user, 'admin');

        // Then:
        $this->seeInDatabase('groupable_roles', [
            'group_id' => $this->school->id,
            'group_type' => get_class($this->school),
            'user_id' => $this->user->id,
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function revoke_member_role()
    {
        // Given:
        $this->build();
        $this->school->grant($this->user, 'admin');
        $this->seeInDatabase('groupable_roles', [
            'group_id' => $this->school->id,
            'group_type' => get_class($this->school),
            'user_id' => $this->user->id,
            'role' => 'admin',
        ]);

        // When:
        $this->school->revoke($this->user, 'admin');

        // Then:
        $this->dontSeeInDatabase('groupable_roles', [
            'group_id' => $this->school->id,
            'group_type' => get_class($this->school),
            'user_id' => $this->user->id,
            'role' => 'admin',
        ]);
    }

    /**
     * @test
     * @expectedException  Exception
     * @expectedExceptionMessage  Role non-existant-role does not exist in group of type App\School.
     */
    public function granting_undefined_group_role_throws_an_exception()
    {
        // Given:
        $this->build();

        // When:
        $this->school->grant($this->user, 'non-existant-role');

        // Then:
        # Exception should be thrown.
    }
}
