<?php

namespace Etsh\Groupable;

class Groupable
{
    /**
     * Return a model from a type and id.
     * API: $resolveModel($model_type, $model_id)
     *
     * @return  array
     */
    public static function resolveModel($model_type, $model_id)
    {
        return $model_type::find($model_id);
    }

    /**
     * Return an array for a where clause.
     * API: $whereClause($array)
     *
     * @return  array
     */
    public static function whereClause($column, $array)
    {
        $queryArray = [];

        foreach ($array as $value) {
            $queryArray[] = [$column, '=', $value];
        }

        return $queryArray;
    }
}
