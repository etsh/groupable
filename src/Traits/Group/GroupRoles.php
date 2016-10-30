<?php

namespace Etsh\Groupable\Traits\Group;

use DB;
use App\User;
use Exception;
use Carbon\Carbon;
use Etsh\Groupable\Groupable;

trait GroupRoles
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

}
