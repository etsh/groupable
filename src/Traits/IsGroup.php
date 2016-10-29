<?php

namespace Etsh\Groupable\Traits;

use DB;
use App\User;
use Exception;
use Carbon\Carbon;
use Etsh\Groupable\Groupable;

/*
|--------------------------------------------------------------------------
| IsGroup
|--------------------------------------------------------------------------
|
| Use this trait in one of your models to turn it into a group. You may also
| add the protected properties $groupable_roles and $groupable_content.
| These enable you to assign users roles and control which models
| can be added to this group.
|
*/

trait IsGroup
{
    /**
     * Return all group roles.
     * API: $group->roles()
     *
     * @return  array
     */
    public function roles()
    {
        return $this->groupable_roles;
    }

    /**
     * Return all groupable models.
     * API: $group->types()
     *
     * @return  array
     */
    public function types()
    {
        return $this->groupable_models;
    }

    /**
     * Join a group
     * API: $group->join($user)
     *
     * @return  bool
     */
    public function join($user)
    {
        return DB::table('groupable_members')->insert([
            'group_id' => $this->id,
            'group_type' => get_class($this),
            'user_id' => $user->id,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    /**
     * Leave a group
     * API: $group->leave($user)
     *
     * @return  int
     */
    public function leave($user)
    {
        return DB::table('groupable_members')->where([
            ['group_id', '=', $this->id],
            ['group_type', '=', get_class($this)],
            ['user_id', '=', $user->id],
        ])->delete();
    }

    /**
     * Grant a group role.
     * API: $group->grant($user, $role)
     *
     * @return  bool
     */
    public function grant($user, $role)
    {
        if ($this->validRole($role) && $user->belongsToGroup($this)) {
            return DB::table('groupable_roles')->insert([
                'group_id' => $this->id,
                'group_type' => get_class($this),
                'user_id' => $user->id,
                'role' => $role,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        }
        else {
            throw new Exception("Role $role does not exist in group of type " . get_class($this) . ".");
        }
    }

    /**
     * Revoke a group role.
     * API: $group->revoke($user, $role)
     *
     * @return  int
     */
    public function revoke($user, $role)
    {
        if ($this->validRole($role) && $user->belongsToGroup($this)) {
            return DB::table('groupable_roles')->where([
                ['group_id', '=', $this->id],
                ['group_type', '=', get_class($this)],
                ['user_id', '=', $user->id],
                ['role', '=', $role],
            ])->delete();
        }
        else {
            throw new Exception("Role $role does not exist in group of type " . get_class($this) . ".");
        }
    }

    /**
    * Check role is a valid group role.
    * API: $group->validRole($role)
    *
    * @return  bool
    */
    protected function validRole($role)
    {
        return in_array($role, $this->roles());
    }

    /**
    * Add content to group.
    * API: $group->addContent($content)
    *
    * @return  bool
    */
    public function addContent($content)
    {
        if (in_array(get_class($content), $this->groupable_models)) {
            return DB::table('groupables')->insert([
                'group_id' => $this->id,
                'group_type' => get_class($this),
                'groupable_id' => $content->id,
                'groupable_type' => get_class($content),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        }
        else {
            throw new Exception("Content of type " . get_class($content) . " can not be added to group of type " . get_class($this) . ".");
        }
    }

    /**
    * Remove content from group.
    * API: $group->removeContent($content)
    *
    * @return  int
    */
    public function removeContent($content)
    {
        return DB::table('groupables')->where([
            ['group_id', '=', $this->id],
            ['group_type', '=', get_class($this)],
            ['groupable_id', '=', $content->id],
            ['groupable_type', '=', get_class($content)],
            ])->delete();
        }

    /**
     * Return all group members - TODO: optionally restrict by role.
     * API: $group->members()
     *
     * @param  array
     * @return  array
     */
    public function members(array $roles = [])
    {
        $collection = collect([]);

        $members = DB::table('groupable_members')
                    ->select('user_id')
                    ->where('group_id', '=', $this->id)
                    ->where('group_type', '=', get_class($this))
                    ->get();

        foreach ($members as $member) {
            $collection->push(User::find($member->user_id));
        }

        foreach($roles as $role) {
            $collection->filter(function ($value, $key) use ($role) {
                return $value->hasGroupRole($this, $role);
            });
        }

        return $collection;
    }

    /**
     * Return all group content - optionally restrict by type.
     * API: $group->content()
     *
     * @param  array
     * @return  array
     */
    public function content(array $types = [])
    {
        $contents = DB::table('groupables')
                    ->select('groupable_id', 'groupable_type')
                    ->where([
                        ['group_id', '=', $this->id],
                        ['group_type', '=', get_class($this)],
                    ])
                    ->where(Groupable::whereClause('groupable_type', $types))
                    ->get();

        $collection = collect([]);

        foreach ($contents as $content) {
            $collection->push(Groupable::resolveModel($content->groupable_type, $content->groupable_id));
        }

        return $collection;
    }
}
