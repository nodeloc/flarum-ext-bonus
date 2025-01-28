<?php

namespace Nodeloc\Bonus\Controllers;


use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Nodeloc\Bonus\BonusListItem;
use Nodeloc\Bonus\Serializers\BonusListItemSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class BonusListController extends AbstractListController
{
    public $serializer = BonusListItemSerializer::class;

    public $include = [
        'group',
        'members.groups',
    ];

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertCan('bonus-list.see');

        $items = BonusListItem::query()->get();

        $items->load([
            'group',
        ]);

        return $items;
    }
}
