<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;
use App\School;

class ListUsersGroupRolesTest extends TestCase
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
        $this->school->grant($this->user, 'admin');
    }

    /** @test */
    public function get_all_group_roles_for_a_user()
    {
        // Given:
        $this->build();

        // When:
        $result = $this->user->groupRoles($this->school);

        // Then:
        $this->assertEquals($result[0], 'member');
        $this->assertEquals($result[1], 'admin');
    }

    /**
     * @test
     * @expectedException  Exception
     * @expectedExceptionMessage  User is not a member of this group.
     */
    public function getting_roles_for_group_which_user_does_not_belong_to_throws_an_exception()
    {
        // Given:
        $this->build();
        $this->school->revoke($this->user, 'admin');
        $this->school->leave($this->user);

        // When:
        $result = $this->user->groupRoles($this->school);

        // Then:
        # Exception thrown.
    }
}
