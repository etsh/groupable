<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Set;
use App\User;
use App\School;
use App\Department;

class AddRemoveAndRetrieveGroupContentTest extends TestCase
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
    }

    /** @test */
    public function add_group_content()
    {
        // Given:
        $this->build();

        // When:
        $this->school->addContent($this->department);

        // Then:
        $this->seeInDatabase('groupables', [
            'group_id' => $this->school->id,
            'group_type' => 'App\School',
            'groupable_id' => $this->department->id,
            'groupable_type' => 'App\Department',
        ]);
    }

    /** @test */
    public function retrieve_group_content()
    {
        // Given:
        $this->build();
        $this->school->addContent($this->department);

        // When:
        $school_content = $this->school->content();

        // Then:
        $this->assertInstanceOf('App\Department', $school_content->first());
    }

    /** @test */
    public function retrieve_group_content_of_given_type()
    {
        // TODO: This
    }

    /** @test */
    public function remove_group_content()
    {
        // Given:
        $this->build();
        $this->school->addContent($this->department);
        $this->seeInDatabase('groupables', [
            'group_id' => $this->school->id,
            'group_type' => 'App\School',
            'groupable_id' => $this->department->id,
            'groupable_type' => 'App\Department',
        ]);

        // When:
        $this->school->removeContent($this->department);

        // Then:
        $this->dontSeeInDatabase('groupables', [
            'group_id' => $this->school->id,
            'group_type' => 'App\School',
            'groupable_id' => $this->department->id,
            'groupable_type' => 'App\Department',
        ]);
    }

    /**
     * @test
     * @expectedException  Exception
     * @expectedExceptionMessage  Content of type App\Set can not be added to group of type App\School.
     */
    public function adding_non_groupable_model_throws_an_exception()
    {
        // Given:
        $this->build();

        $set = Set::create([
            'name' => 'A1',
            'user_id' => $this->user->id,
        ]);

        // When:
        $this->school->addContent($set);

        // Then:
        # Exception should be thrown.
    }
}
