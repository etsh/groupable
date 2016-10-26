<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;
use App\School;

class CheckUserBelongsToGroupTest extends TestCase
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
    public function belongs_to_group_returns_true_when_user_is_group_member()
    {
        // Given:
        $this->build();
        $this->school->join($this->user);

        // When:
        $result = $this->user->belongsToGroup($this->school);

        // Then:
        $this->assertTrue($result);
    }

    /** @test */
    public function belongs_to_group_returns_false_when_user_is_not_group_member()
    {
        // Given:
        $this->build();

        // When:
        $result = $this->user->belongsToGroup($this->school);

        // Then:
        $this->assertFalse($result);
    }
}
