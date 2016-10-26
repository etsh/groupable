<?php

namespace Etsh\Groupable;

class Groupable
{
    public static function resolveModel ($group_type, $group_id)
    {
        return $group_type::find($group_id);
    }
}
