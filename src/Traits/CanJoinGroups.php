<?php

namespace Etsh\Groupable\Traits;

use DB;
use Exception;
use Etsh\Groupable\Groupable;

/*
|--------------------------------------------------------------------------
| CanJoinGroups
|--------------------------------------------------------------------------
|
| Use this trait in your user model in order to provide group
| functionality to your users.
|
*/

Trait CanJoinGroups
{
    /**
     * Get all user's groups.
     * API: $user->groups()
     *
     * @return array
     */
    public function groups()
    {
        $collection = collect([]);

        $groups = DB::table('groupable_members')->where([
            ['user_id', '=', $this->id],
        ])->get();

        foreach($groups as $group) {
            $collection->push(Groupable::resolveModel($group->group_type, $group->group_id));
        }

        return $collection;
    }

    /**
     * Check if a user belongs to a particular group.
     * API: $user->belongsToGroup($group)
     *
     * @return bool
     */
    public function belongsToGroup($group)
    {
        $check = DB::table('groupable_members')->where([
            ['group_id', '=', $group->id],
            ['group_type', '=', get_class($group)],
            ['user_id', '=', $this->id],
        ])->get();

        if($check->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Check if a user has a group role.
     * API: $user->hasGroupRole($group, $role)
     *
     * @return bool
     */
    public function hasGroupRole($group, $role)
    {
        $check = DB::table('groupable_roles')->where([
            'group_id' => $group->id,
            'group_type' => get_class($group),
            'user_id' => $this->id,
            'role' => $role,
        ])->get();

        if($check->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve the roles a user has within a given group.
     * API: $user->hasGroupRole($group, $role)
     *
     * @return bool
     */
    public function groupRoles($group)
    {
        // TODO: Retrieve all group roles for a given user and group.
        $collection = collect(['member']);

        if ($this->belongsToGroup($group)) {
            $roles = DB::table('groupable_roles')->where([
                'group_id' => $group->id,
                'group_type' => get_class($group),
                'user_id' => $this->id,
            ])->get();

            foreach ($roles as $role) {
                $collection->push($role->role);
            }

            return $collection;
        }
        else {
            throw new Exception("User is not a member of this group.");
        }
    }
}
