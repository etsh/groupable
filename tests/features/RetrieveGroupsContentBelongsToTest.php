<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;
use App\School;
use App\Department;

class RetrieveGroupsContentBelongsToTest extends TestCase
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

        $this->department = Department::create([
            'name' => 'Physics',
            'user_id' => $this->user->id,
        ]);

        $this->school->addContent($this->department);
    }

    /** @test */
    public function testExample()
    {
        // Given:
        $this->build();

        // When:
        $result = $this->department->groups();

        // Then:
        $this->assertEquals(1, $result->count());
    }
}
