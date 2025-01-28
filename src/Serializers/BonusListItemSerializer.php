<?php

namespace Nodeloc\Bonus\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\GroupSerializer;
use Nodeloc\Bonus\BonusListItem;
use Tobscure\JsonApi\Relationship;

class BonusListItemSerializer extends AbstractSerializer
{
    protected $type = 'bonus-list-items';

    /**
     * @param BonusListItem $item
     * @return array
     */
    protected function getDefaultAttributes($item): array
    {
        $attributes = [
            'amount' => (int)$item->amount,
            'content' => $item->content,
            'schedule_type' => (int)$item->schedule_type,
            'schedule_time' => $item->schedule_time,
            'last_post_time' =>$item->last_post_time,
        ];

        return $attributes;
    }

    public function group($item): ?Relationship
    {
        return $this->hasOne($item, GroupSerializer::class);
    }
}
