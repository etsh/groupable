<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;
use App\School;

class CheckUserHasGroupRoleTest extends TestCase
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
    public function check_member_has_a_given_group_role()
    {
        // Given:
        $this->build();

        // When:
        $result = $this->user->hasGroupRole($this->school, 'admin');

        // Then:
        $this->assertTrue($result);
    }

    /** @test */
    public function check_member_does_not_have_a_given_group_role()
    {
        // Given:
        $this->build();
        $this->school->revoke($this->user, 'admin');

        // When:
        $result = $this->user->hasGroupRole($this->school, 'admin');

        // Then:
        $this->assertFalse($result);
    }
}
