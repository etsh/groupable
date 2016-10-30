<?php

namespace Etsh\Groupable\Traits\Group;

use DB;
use App\User;
use Exception;
use Carbon\Carbon;
use Etsh\Groupable\Groupable;

trait GroupMembers
{
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
     * Return all group members.
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

        return $collection;
    }

    /**
     * Return all group members with a given role.
     * API: $group->members()
     *
     * @param  string
     * @return  array
     */
    public function membersByRole(string $role)
    {
        $collection = collect([]);

        $members = DB::table('groupable_roles')
                    ->select('user_id')
                    ->where('group_id', '=', $this->id)
                    ->where('group_type', '=', get_class($this))
                    ->where('role', '=', $role)
                    ->get();

        foreach ($members as $member) {
            $collection->push(User::find($member->user_id));
        }

        return $collection;
    }
}
