<?php

namespace Nodeloc\Bonus\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Group\Group;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Nodeloc\Bonus\BonusListItem;
use Nodeloc\Bonus\Serializers\BonusListItemSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ItemStoreController extends AbstractCreateController
{
    public $serializer = BonusListItemSerializer::class;

    public $include = [
        'group',
    ];

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $group = Group::query()->findOrFail(Arr::get($request->getParsedBody(), 'data.attributes.groupId'));

        $item = new BonusListItem();
        $item->group()->associate($group);
        $item->save();

        return $item;
    }
}
