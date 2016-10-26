<?php

namespace Etsh\Groupable\Traits;

use DB;
use Carbon\Carbon;

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
        $this_class = get_class($this);
        $this_id = $this->id;

        // TODO: Find all items on the groupable table that match the above.
    }
}
