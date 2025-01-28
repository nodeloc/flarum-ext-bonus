<?php

namespace Nodeloc\Bonus;

use Flarum\Database\AbstractModel;
use Flarum\Group\Group;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property int $id
 * @property int $group_id
 * @property string $content
 * @property int $order
 *
 * @property Group $group
 */
class BonusListItem extends AbstractModel
{
    protected $table = 'bonus_list';
    public function group(): Relations\BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

}
