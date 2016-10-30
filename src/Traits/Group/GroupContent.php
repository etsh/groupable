<?php

namespace Etsh\Groupable\Traits\Group;

use DB;
use App\User;
use Exception;
use Carbon\Carbon;
use Etsh\Groupable\Groupable;

trait GroupContent
{
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
