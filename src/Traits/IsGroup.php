<?php

namespace Etsh\Groupable\Traits;

use Etsh\Groupable\Traits\Group\GroupContent;
use Etsh\Groupable\Traits\Group\GroupMembers;
use Etsh\Groupable\Traits\Group\GroupRoles;

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
    use GroupContent;
    use GroupMembers;
    use GroupRoles;
}
