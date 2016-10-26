<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Set;
use App\User;
use App\School;
use App\Department;

class GroupableTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function groupable_models_can_be_added_to_and_removed_from_groups()
    {
        // Create the model structure.
        $user = User::create([
            'name' => 'John Lennon',
            'email' => 'john@example.com',
            'password' => 'secret',
        ]);

        $school = School::create([
            'name' => 'Standard Academy',
            'user_id' => $user->id,
        ]);

        $department = Department::create([
            'name' => 'Physics',
            'user_id' => $user->id,
        ]);

        $set = Set::create([
            'name' => 'A1',
            'user_id' => $user->id,
        ]);

        $this->seeInDatabase('users', ['name' => 'John Lennon']);
        $this->seeInDatabase('schools', ['name' => 'Standard Academy']);
        $this->seeInDatabase('departments', ['name' => 'Physics']);
        $this->seeInDatabase('sets', ['name' => 'A1']);

        // Add group content like this.
        $department->addContent($set);
        $school->addContent($department);

        $this->seeInDatabase('groupables', [
            'group_id' => $department->id,
            'group_type' => 'App\Department',
            'groupable_id' => $set->id,
            'groupable_type' => 'App\Set',
        ]);

        $this->seeInDatabase('groupables', [
            'group_id' => $school->id,
            'group_type' => 'App\School',
            'groupable_id' => $department->id,
            'groupable_type' => 'App\Department',
        ]);

        // Retrieve group content like this.
        $school_content = $school->content();
        $department_content = $department->content();

        $this->assertInstanceOf('App\Department', $school_content->first());
        $this->assertInstanceOf('App\Set', $department_content->first());

        // Remove group content like this.
        $department->removeContent($set);
        $school->removeContent($department);

        $this->dontSeeInDatabase('groupables', [
            'group_id' => $department->id,
            'group_type' => 'App\Department',
            'groupable_id' => $set->id,
            'groupable_type' => 'App\Set',
        ]);

        $this->dontSeeInDatabase('groupables', [
            'group_id' => $school->id,
            'group_type' => 'App\School',
            'groupable_id' => $department->id,
            'groupable_type' => 'App\Department',
        ]);
    }

    /** @test */
    public function users_can_join_and_leave_groups()
    {
        // Create the model structure.
        $user = User::create([
            'name' => 'John Lennon',
            'email' => 'john@example.com',
            'password' => 'secret',
        ]);

        $school = School::create([
            'name' => 'Standard Academy',
            'user_id' => $user->id,
        ]);

        $school->join($user);

        $this->seeInDatabase('groupable_members', [
            'group_id' => $school->id,
            'group_type' => get_class($school),
            'user_id' => $user->id,
        ]);

        $members = $school->members();
        $this->assertInstanceOf('App\User', $members->first());

        $school->grant($user, 'admin');

        $this->seeInDatabase('groupable_roles', [
            'group_id' => $school->id,
            'group_type' => get_class($school),
            'user_id' => $user->id,
            'role' => 'admin',
        ]);

        $school->revoke($user, 'admin');

        $this->dontSeeInDatabase('groupable_roles', [
            'group_id' => $school->id,
            'group_type' => get_class($school),
            'user_id' => $user->id,
            'role' => 'admin',
        ]);

        $school->leave($user);

        $this->dontSeeInDatabase('groupable_members', [
            'group_id' => $school->id,
            'group_type' => get_class($school),
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function the_correct_users_are_returned_with_their_roles()
    {

    }
}
