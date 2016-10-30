<?php

namespace Etsh\Groupable\Traits;

use DB;
use Carbon\Carbon;
use Etsh\Groupable\Groupable;

/*
|--------------------------------------------------------------------------
| IsGroupable
|--------------------------------------------------------------------------
|
| Use this trait in one of your models in order to make it groupable.
|
*/

Trait IsGroupable
{
    /**
     * Get Groups it Belongs To
     * API: $model->groups()
     *
     * @return array
     */
    public function groups()
    {
        $collection = collect([]);

        $groups = DB::table('groupables')
                    ->select('group_id', 'group_type')
                    ->where('groupable_id', '=', $this->id)
                    ->where('groupable_type', '=', get_class($this))
                    ->get();

        foreach ($groups as $group) {
            $collection->push(Groupable::resolveModel($group->group_type, $group->group_id));
        }

        return $collection;
    }
}
